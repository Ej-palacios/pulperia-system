<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gasto extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'descripcion',
        'categoria',
        'monto',
        'fecha',
        'comprobante',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    /**
     * Relación con el usuario que registró el gasto
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para gastos por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para gastos de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para gastos del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('fecha', $fecha);
    }

    /**
     * Scope para gastos de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para gastos del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('fecha', $year)
                    ->whereMonth('fecha', $month);
    }

    /**
     * Scope para gastos por usuario
     */
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para buscar por descripción
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('descripcion', 'like', "%{$termino}%")
                    ->orWhere('observaciones', 'like', "%{$termino}%");
    }

    /**
     * Verificar si el gasto tiene comprobante
     */
    public function tieneComprobante()
    {
        return !empty($this->comprobante);
    }

    /**
     * Obtener las categorías disponibles
     */
    public static function getCategorias()
    {
        return [
            'alquiler' => 'Alquiler',
            'luz' => 'Luz',
            'agua' => 'Agua',
            'transporte' => 'Transporte',
            'mantenimiento' => 'Mantenimiento',
            'otros' => 'Otros',
        ];
    }

    /**
     * Obtener el nombre de la categoría
     */
    public function getNombreCategoriaAttribute()
    {
        $categorias = self::getCategorias();
        return $categorias[$this->categoria] ?? $this->categoria;
    }

    /**
     * Obtener el total de gastos por categoría
     */
    public static function getTotalPorCategoria($fechaInicio = null, $fechaFin = null)
    {
        $query = self::query();
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->selectRaw('categoria, SUM(monto) as total')
                    ->groupBy('categoria')
                    ->orderBy('total', 'desc')
                    ->get();
    }

    /**
     * Obtener el total de gastos del mes
     */
    public static function getTotalDelMes($year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return self::delMes($year, $month)->sum('monto');
    }

    /**
     * Obtener el total de gastos de la semana
     */
    public static function getTotalDeLaSemana()
    {
        return self::deLaSemana()->sum('monto');
    }

    /**
     * Obtener el total de gastos del día
     */
    public static function getTotalDelDia($fecha = null)
    {
        return self::delDia($fecha)->sum('monto');
    }
}
