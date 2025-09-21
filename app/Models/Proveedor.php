<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'ruc',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con las compras del proveedor
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Scope para proveedores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre, contacto o teléfono
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('contacto', 'like', "%{$termino}%")
                    ->orWhere('telefono', 'like', "%{$termino}%")
                    ->orWhere('email', 'like', "%{$termino}%");
    }

    /**
     * Obtener el total de compras del proveedor
     */
    public function getTotalComprasAttribute()
    {
        return $this->compras()->sum('total');
    }

    /**
     * Obtener el número de compras del proveedor
     */
    public function getNumeroComprasAttribute()
    {
        return $this->compras()->count();
    }

    /**
     * Obtener la última compra del proveedor
     */
    public function getUltimaCompraAttribute()
    {
        return $this->compras()->latest()->first();
    }

    /**
     * Verificar si el proveedor tiene compras
     */
    public function tieneCompras()
    {
        return $this->compras()->exists();
    }
}
