<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'subtotal',
        'impuestos',
        'total',
        'tipo_pago',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con el usuario que realizó la venta
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los detalles de la venta
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación con el crédito (si aplica)
     */
    public function credito(): HasOne
    {
        return $this->hasOne(Credito::class);
    }

    /**
     * Scope para ventas por tipo de pago
     */
    public function scopePorTipoPago($query, $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    /**
     * Scope para ventas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para ventas de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para ventas del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para ventas de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para ventas del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
    }

    /**
     * Verificar si la venta es de contado
     */
    public function esContado()
    {
        return $this->tipo_pago === 'contado';
    }

    /**
     * Verificar si la venta es a crédito
     */
    public function esCredito()
    {
        return $this->tipo_pago === 'credito';
    }

    /**
     * Verificar si la venta está completada
     */
    public function estaCompletada()
    {
        return $this->estado === 'completada';
    }

    /**
     * Verificar si la venta está anulada
     */
    public function estaAnulada()
    {
        return $this->estado === 'anulada';
    }

    /**
     * Obtener el número de productos vendidos
     */
    public function getCantidadProductosAttribute()
    {
        return $this->detalles()->sum('cantidad');
    }

    /**
     * Obtener el margen de ganancia de la venta
     */
    public function getMargenGananciaAttribute()
    {
        $costoTotal = $this->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->producto->costo_compra;
        });
        
        return $this->subtotal - $costoTotal;
    }

    /**
     * Obtener el porcentaje de ganancia
     */
    public function getPorcentajeGananciaAttribute()
    {
        if ($this->subtotal > 0) {
            return ($this->margen_ganancia / $this->subtotal) * 100;
        }
        return 0;
    }
}
