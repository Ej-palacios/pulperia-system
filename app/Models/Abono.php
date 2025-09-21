<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    use HasFactory;

    protected $fillable = [
        'credito_id',
        'cliente_id',
        'user_id',
        'monto',
        'metodo_pago',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    /**
     * Relación con el crédito
     */
    public function credito(): BelongsTo
    {
        return $this->belongsTo(Credito::class);
    }

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con el usuario que registró el abono
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para abonos por método de pago
     */
    public function scopePorMetodoPago($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    /**
     * Scope para abonos de un cliente específico
     */
    public function scopeDelCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para abonos de un crédito específico
     */
    public function scopeDelCredito($query, $creditoId)
    {
        return $query->where('credito_id', $creditoId);
    }

    /**
     * Scope para abonos de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para abonos del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para abonos de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para abonos del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
    }

    /**
     * Verificar si el abono es en efectivo
     */
    public function esEfectivo()
    {
        return $this->metodo_pago === 'efectivo';
    }

    /**
     * Verificar si el abono es por transferencia
     */
    public function esTransferencia()
    {
        return $this->metodo_pago === 'transferencia';
    }

    /**
     * Verificar si el abono es por datáfono
     */
    public function esDatafono()
    {
        return $this->metodo_pago === 'datáfono';
    }

    /**
     * Obtener el saldo restante del crédito después de este abono
     */
    public function getSaldoRestanteAttribute()
    {
        return $this->credito->saldo_pendiente;
    }

    /**
     * Obtener el porcentaje del crédito que representa este abono
     */
    public function getPorcentajeCreditoAttribute()
    {
        if ($this->credito->monto > 0) {
            return ($this->monto / $this->credito->monto) * 100;
        }
        return 0;
    }
}
