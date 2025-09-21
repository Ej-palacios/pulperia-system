<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoDanado extends Model
{
    use HasFactory;

    protected $table = 'productos_danados';

    protected $fillable = [
        'producto_id',
        'user_id',
        'cantidad',
        'motivo',
        'costo_perdida',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_perdida' => 'decimal:2',
    ];

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con el usuario que registró el producto dañado
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para productos dañados de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para productos dañados del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para productos dañados de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para productos dañados del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
    }

    /**
     * Scope para productos dañados de un producto específico
     */
    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para productos dañados de un usuario específico
     */
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para buscar por motivo
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('motivo', 'like', "%{$termino}%")
                    ->orWhere('observaciones', 'like', "%{$termino}%");
    }

    /**
     * Obtener el total de productos dañados por producto
     */
    public static function getTotalPorProducto($fechaInicio = null, $fechaFin = null)
    {
        $query = self::with('producto');
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->selectRaw('producto_id, SUM(cantidad) as total_cantidad, SUM(costo_perdida) as total_costo')
                    ->groupBy('producto_id')
                    ->orderBy('total_costo', 'desc')
                    ->get();
    }

    /**
     * Obtener el total de productos dañados por usuario
     */
    public static function getTotalPorUsuario($fechaInicio = null, $fechaFin = null)
    {
        $query = self::with('user');
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->selectRaw('user_id, SUM(cantidad) as total_cantidad, SUM(costo_perdida) as total_costo')
                    ->groupBy('user_id')
                    ->orderBy('total_costo', 'desc')
                    ->get();
    }

    /**
     * Obtener el total de pérdidas del mes
     */
    public static function getTotalPerdidasDelMes($year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return self::delMes($year, $month)->sum('costo_perdida');
    }

    /**
     * Obtener el total de pérdidas de la semana
     */
    public static function getTotalPerdidasDeLaSemana()
    {
        return self::deLaSemana()->sum('costo_perdida');
    }

    /**
     * Obtener el total de pérdidas del día
     */
    public static function getTotalPerdidasDelDia($fecha = null)
    {
        return self::delDia($fecha)->sum('costo_perdida');
    }

    /**
     * Obtener el promedio de pérdidas por producto dañado
     */
    public function getPromedioPerdidaAttribute()
    {
        return $this->cantidad > 0 ? $this->costo_perdida / $this->cantidad : 0;
    }
}
