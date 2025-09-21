<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación con los productos de la categoría
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Scope para buscar por nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('descripcion', 'like', "%{$termino}%");
    }

    /**
     * Obtener el número de productos activos en esta categoría
     */
    public function getProductosActivosCountAttribute()
    {
        return $this->productos()->where('activo', true)->count();
    }

    /**
     * Obtener el valor total del inventario de esta categoría
     */
    public function getValorInventarioAttribute()
    {
        return $this->productos()
            ->where('activo', true)
            ->get()
            ->sum(function ($producto) {
                return $producto->stock * $producto->costo_compra;
            });
    }
}
