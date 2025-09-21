<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'apellido',
        'username',
        'password',
        'telefono',
        'role',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    /**
     * Relación con las ventas realizadas por el usuario
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Relación con las compras realizadas por el usuario
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Relación con los abonos registrados por el usuario
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Relación con los gastos registrados por el usuario
     */
    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    /**
     * Relación con los productos dañados registrados por el usuario
     */
    public function productosDanados(): HasMany
    {
        return $this->hasMany(ProductoDanado::class);
    }

    /**
     * Relación con los movimientos de inventario registrados por el usuario
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Relación con los arqueos de caja realizados por el usuario
     */
    public function arqueosCaja(): HasMany
    {
        return $this->hasMany(ArqueoCaja::class);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar por nombre o username
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('name', 'like', "%{$termino}%")
                    ->orWhere('username', 'like', "%{$termino}%");
    }

    /**
     * Verificar si el usuario está activo
     */
    public function estaActivo()
    {
        return $this->activo;
    }

    /**
     * Verificar si el usuario es dueño
     */
    public function esDueño()
    {
        return $this->hasRole('dueño');
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function esAdministrador()
    {
        return $this->hasRole('administrador');
    }

    /**
     * Verificar si el usuario es vendedor
     */
    public function esVendedor()
    {
        return $this->hasRole('vendedor');
    }

    /**
     * Obtener el total de ventas del usuario
     */
    public function getTotalVentasAttribute()
    {
        return $this->ventas()->sum('total');
    }

    /**
     * Obtener el total de compras del usuario
     */
    public function getTotalComprasAttribute()
    {
        return $this->compras()->sum('total');
    }

    /**
     * Obtener el total de abonos registrados por el usuario
     */
    public function getTotalAbonosAttribute()
    {
        return $this->abonos()->sum('monto');
    }

    /**
     * Obtener el total de gastos registrados por el usuario
     */
    public function getTotalGastosAttribute()
    {
        return $this->gastos()->sum('monto');
    }

    /**
     * Obtener las ventas del día del usuario
     */
    public function ventasDelDia()
    {
        return $this->ventas()->whereDate('created_at', now());
    }

    /**
     * Obtener las ventas de la semana del usuario
     */
    public function ventasDeLaSemana()
    {
        return $this->ventas()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Obtener las ventas del mes del usuario
     */
    public function ventasDelMes()
    {
        return $this->ventas()->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
    }
}
