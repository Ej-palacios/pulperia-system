<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relación con la venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Obtener la ganancia de este detalle
     */
    public function getGananciaAttribute()
    {
        $costoTotal = $this->cantidad * $this->producto->costo_compra;
        return $this->subtotal - $costoTotal;
    }

    /**
     * Obtener el porcentaje de ganancia de este detalle
     */
    public function getPorcentajeGananciaAttribute()
    {
        if ($this->subtotal > 0) {
            return ($this->ganancia / $this->subtotal) * 100;
        }
        return 0;
    }

    /**
     * Obtener el precio de costo total
     */
    public function getCostoTotalAttribute()
    {
        return $this->cantidad * $this->producto->costo_compra;
    }
}
