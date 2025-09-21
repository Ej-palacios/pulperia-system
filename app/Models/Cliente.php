<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula',
        'telefono',
        'direccion',
        'email',
        'saldo',
        'activo',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación con los créditos del cliente
     */
    public function creditos(): HasMany
    {
        return $this->hasMany(Credito::class);
    }

    /**
     * Relación con los abonos del cliente
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Relación con las ventas del cliente
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Scope para clientes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre, cédula o teléfono
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('cedula', 'like', "%{$termino}%")
                    ->orWhere('telefono', 'like', "%{$termino}%");
    }

    /**
     * Obtener el saldo total del cliente
     */
    public function getSaldoTotalAttribute()
    {
        return $this->creditos()->sum('monto') - $this->abonos()->sum('monto');
    }

    /**
     * Verificar si el cliente tiene saldo pendiente
     */
    public function tieneSaldoPendiente()
    {
        return $this->saldo_total > 0;
    }
}
