<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'user_id',
        'cantidad',
        'tipo',
        'motivo',
        'referencia_type',
        'referencia_id',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con el usuario que registró el movimiento
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación polimórfica con el modelo que generó el movimiento
     */
    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para movimientos de entrada
     */
    public function scopeEntradas($query)
    {
        return $query->where('tipo', 'entrada');
    }

    /**
     * Scope para movimientos de salida
     */
    public function scopeSalidas($query)
    {
        return $query->where('tipo', 'salida');
    }

    /**
     * Scope para movimientos de un producto específico
     */
    public function scopeDelProducto($query, $productoId)
    {
        return $query->where('producto_id', $productoId);
    }

    /**
     * Scope para movimientos de un usuario específico
     */
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para movimientos de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para movimientos del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para movimientos de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para movimientos del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
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
     * Verificar si es un movimiento de entrada
     */
    public function esEntrada()
    {
        return $this->tipo === 'entrada';
    }

    /**
     * Verificar si es un movimiento de salida
     */
    public function esSalida()
    {
        return $this->tipo === 'salida';
    }

    /**
     * Obtener el impacto en el stock (positivo para entradas, negativo para salidas)
     */
    public function getImpactoStockAttribute()
    {
        return $this->esEntrada() ? $this->cantidad : -$this->cantidad;
    }

    /**
     * Obtener el total de entradas de un producto
     */
    public static function getTotalEntradas($productoId, $fechaInicio = null, $fechaFin = null)
    {
        $query = self::delProducto($productoId)->entradas();
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->sum('cantidad');
    }

    /**
     * Obtener el total de salidas de un producto
     */
    public static function getTotalSalidas($productoId, $fechaInicio = null, $fechaFin = null)
    {
        $query = self::delProducto($productoId)->salidas();
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->sum('cantidad');
    }

    /**
     * Obtener el saldo actual de un producto
     */
    public static function getSaldoActual($productoId, $fechaInicio = null, $fechaFin = null)
    {
        $entradas = self::getTotalEntradas($productoId, $fechaInicio, $fechaFin);
        $salidas = self::getTotalSalidas($productoId, $fechaInicio, $fechaFin);
        
        return $entradas - $salidas;
    }

    /**
     * Obtener los tipos de movimiento disponibles
     */
    public static function getTipos()
    {
        return [
            'entrada' => 'Entrada',
            'salida' => 'Salida',
        ];
    }

    /**
     * Obtener el nombre del tipo de movimiento
     */
    public function getNombreTipoAttribute()
    {
        $tipos = self::getTipos();
        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Obtener el resumen de movimientos por producto
     */
    public static function getResumenPorProducto($fechaInicio = null, $fechaFin = null)
    {
        $query = self::with('producto');
        
        if ($fechaInicio && $fechaFin) {
            $query->porFechas($fechaInicio, $fechaFin);
        }
        
        return $query->selectRaw('producto_id, tipo, SUM(cantidad) as total_cantidad')
                    ->groupBy('producto_id', 'tipo')
                    ->orderBy('producto_id')
                    ->get()
                    ->groupBy('producto_id');
    }
}
