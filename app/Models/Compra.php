<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'numero_factura',
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
     * Relación con el proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con el usuario que realizó la compra
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los detalles de la compra
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Scope para compras por tipo de pago
     */
    public function scopePorTipoPago($query, $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    /**
     * Scope para compras por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para compras de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para compras del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para compras de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para compras del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
    }

    /**
     * Verificar si la compra es de contado
     */
    public function esContado()
    {
        return $this->tipo_pago === 'contado';
    }

    /**
     * Verificar si la compra es a crédito
     */
    public function esCredito()
    {
        return $this->tipo_pago === 'credito';
    }

    /**
     * Verificar si la compra está completada
     */
    public function estaCompletada()
    {
        return $this->estado === 'completada';
    }

    /**
     * Verificar si la compra está recibida
     */
    public function estaRecibida()
    {
        return $this->estado === 'recibida';
    }

    /**
     * Obtener el número de productos comprados
     */
    public function getCantidadProductosAttribute()
    {
        return $this->detalles()->sum('cantidad');
    }

    /**
     * Obtener el valor promedio por producto
     */
    public function getValorPromedioProductoAttribute()
    {
        $cantidadProductos = $this->cantidad_productos;
        return $cantidadProductos > 0 ? $this->total / $cantidadProductos : 0;
    }
}
