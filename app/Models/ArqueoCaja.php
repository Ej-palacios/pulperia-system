<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArqueoCaja extends Model
{
    use HasFactory;

    protected $table = 'arqueo_caja';

    protected $fillable = [
        'user_id',
        'efectivo_inicial',
        'efectivo_final',
        'ventas_contado',
        'otros_ingresos',
        'otros_gastos',
        'total_teorico',
        'diferencia',
        'observaciones',
    ];

    protected $casts = [
        'efectivo_inicial' => 'decimal:2',
        'efectivo_final' => 'decimal:2',
        'ventas_contado' => 'decimal:2',
        'otros_ingresos' => 'decimal:2',
        'otros_gastos' => 'decimal:2',
        'total_teorico' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    /**
     * Relación con el usuario que realizó el arqueo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para arqueos de un rango de fechas
     */
    public function scopePorFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para arqueos del día
     */
    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?: now();
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Scope para arqueos de la semana
     */
    public function scopeDeLaSemana($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope para arqueos del mes
     */
    public function scopeDelMes($query, $year = null, $month = null)
    {
        $year = $year ?: now()->year;
        $month = $month ?: now()->month;
        
        return $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
    }

    /**
     * Scope para arqueos con diferencia
     */
    public function scopeConDiferencia($query)
    {
        return $query->where('diferencia', '!=', 0);
    }

    /**
     * Scope para arqueos sin diferencia
     */
    public function scopeSinDiferencia($query)
    {
        return $query->where('diferencia', 0);
    }

    /**
     * Verificar si hay diferencia en el arqueo
     */
    public function tieneDiferencia()
    {
        return $this->diferencia != 0;
    }

    /**
     * Verificar si hay sobrante
     */
    public function tieneSobrante()
    {
        return $this->diferencia > 0;
    }

    /**
     * Verificar si hay faltante
     */
    public function tieneFaltante()
    {
        return $this->diferencia < 0;
    }

    /**
     * Obtener el total de ingresos
     */
    public function getTotalIngresosAttribute()
    {
        return $this->ventas_contado + $this->otros_ingresos;
    }

    /**
     * Obtener el total de egresos
     */
    public function getTotalEgresosAttribute()
    {
        return $this->otros_gastos;
    }

    /**
     * Obtener el flujo neto
     */
    public function getFlujoNetoAttribute()
    {
        return $this->total_ingresos - $this->total_egresos;
    }

    /**
     * Obtener el porcentaje de diferencia
     */
    public function getPorcentajeDiferenciaAttribute()
    {
        if ($this->total_teorico > 0) {
            return ($this->diferencia / $this->total_teorico) * 100;
        }
        return 0;
    }

    /**
     * Obtener el estado del arqueo
     */
    public function getEstadoAttribute()
    {
        if ($this->diferencia == 0) {
            return 'correcto';
        } elseif ($this->diferencia > 0) {
            return 'sobrante';
        } else {
            return 'faltante';
        }
    }

    /**
     * Calcular el total teórico
     */
    public function calcularTotalTeorico()
    {
        return $this->efectivo_inicial + $this->total_ingresos - $this->total_egresos;
    }

    /**
     * Calcular la diferencia
     */
    public function calcularDiferencia()
    {
        return $this->efectivo_final - $this->total_teorico;
    }
}
