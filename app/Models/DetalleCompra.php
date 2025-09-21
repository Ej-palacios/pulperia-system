<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_compra',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relación con la compra
     */
    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación con el producto
     */
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Obtener el costo total de este detalle
     */
    public function getCostoTotalAttribute()
    {
        return $this->cantidad * $this->precio_compra;
    }

    /**
     * Obtener el precio promedio por unidad
     */
    public function getPrecioPromedioAttribute()
    {
        return $this->cantidad > 0 ? $this->subtotal / $this->cantidad : 0;
    }
}
