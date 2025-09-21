<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_barras',
        'categoria_id',
        'precio_venta',
        'costo_compra',
        'stock',
        'stock_minimo',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'costo_compra' => 'decimal:2',
        'stock' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Obtener URL pública de la imagen (subida o externa)
     */
    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://')) {
            return $this->imagen;
        }

        return asset('storage/' . ltrim($this->imagen, '/'));
    }

    /**
     * Relación con la categoría del producto
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con los detalles de venta
     */
    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación con los detalles de compra
     */
    public function detalleCompras(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Relación con los movimientos de inventario
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Relación con los productos dañados
     */
    public function productosDanados(): HasMany
    {
        return $this->hasMany(ProductoDanado::class);
    }

    /**
     * Relación polimórfica con movimientos de inventario
     */
    public function movimientosInventario(): MorphMany
    {
        return $this->morphMany(MovimientoInventario::class, 'referencia');
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para productos con stock disponible
     */
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope para productos con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock <= stock_minimo');
    }

    /**
     * Scope para buscar por nombre o código de barras
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('codigo_barras', 'like', "%{$termino}%")
                    ->orWhere('descripcion', 'like', "%{$termino}%");
    }

    /**
     * Obtener el valor total del inventario de este producto
     */
    public function getValorInventarioAttribute()
    {
        return $this->stock * $this->costo_compra;
    }

    /**
     * Verificar si el producto tiene stock bajo
     */
    public function tieneStockBajo()
    {
        return $this->stock <= $this->stock_minimo;
    }

    /**
     * Verificar si el producto está disponible para venta
     */
    public function estaDisponible()
    {
        return $this->activo && $this->stock > 0;
    }

    /**
     * Obtener la ganancia por unidad
     */
    public function getGananciaUnidadAttribute()
    {
        return $this->precio_venta - $this->costo_compra;
    }

    /**
     * Obtener el porcentaje de ganancia
     */
    public function getPorcentajeGananciaAttribute()
    {
        if ($this->costo_compra > 0) {
            return (($this->precio_venta - $this->costo_compra) / $this->costo_compra) * 100;
        }
        return 0;
    }
}
