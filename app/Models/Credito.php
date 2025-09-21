<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credito extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'cliente_id',
        'monto',
        'saldo_pendiente',
        'fecha_limite',
        'estado',
        'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_limite' => 'date',
    ];

    /**
     * Relación con la venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con los abonos
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Scope para créditos por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para créditos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    /**
     * Scope para créditos vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('fecha_limite', '<', now())
                    ->where('saldo_pendiente', '>', 0);
    }

    /**
     * Scope para créditos por vencer (próximos 7 días)
     */
    public function scopePorVencer($query, $dias = 7)
    {
        return $query->where('fecha_limite', '<=', now()->addDays($dias))
                    ->where('fecha_limite', '>', now())
                    ->where('saldo_pendiente', '>', 0);
    }

    /**
     * Scope para créditos de un cliente específico
     */
    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Verificar si el crédito está pagado
     */
    public function estaPagado()
    {
        return $this->estado === 'pagado' || $this->saldo_pendiente <= 0;
    }

    /**
     * Verificar si el crédito está vencido
     */
    public function estaVencido()
    {
        return $this->fecha_limite < now() && $this->saldo_pendiente > 0;
    }

    /**
     * Verificar si el crédito está por vencer
     */
    public function estaPorVencer($dias = 7)
    {
        return $this->fecha_limite <= now()->addDays($dias) 
               && $this->fecha_limite > now() 
               && $this->saldo_pendiente > 0;
    }

    /**
     * Obtener el total de abonos realizados
     */
    public function getTotalAbonadoAttribute()
    {
        return $this->abonos()->sum('monto');
    }

    /**
     * Obtener el porcentaje pagado
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->monto > 0) {
            return ($this->total_abonado / $this->monto) * 100;
        }
        return 0;
    }

    /**
     * Obtener los días de vencimiento
     */
    public function getDiasVencimientoAttribute()
    {
        return now()->diffInDays($this->fecha_limite, false);
    }

    /**
     * Obtener el estado del crédito basado en fechas y saldo
     */
    public function getEstadoCalculadoAttribute()
    {
        if ($this->saldo_pendiente <= 0) {
            return 'pagado';
        }

        if ($this->fecha_limite < now()) {
            return 'vencido';
        }

        if ($this->total_abonado > 0) {
            return 'parcialmente_pagado';
        }

        return 'pendiente';
    }

    /**
     * Actualizar el estado del crédito
     */
    public function actualizarEstado()
    {
        $this->estado = $this->estado_calculado;
        $this->save();
    }
}
