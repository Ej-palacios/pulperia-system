<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionTienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'impuesto',
        'moneda',
        'telefono',
        'direccion',
        'mensaje_ticket',
        'logo',
        'activo',
    ];

    protected $casts = [
        'impuesto' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Obtener la configuración actual de la tienda
     */
    public static function getConfiguracion()
    {
        return self::first() ?? self::create([
            'nombre' => 'Pulpería Managua',
            'impuesto' => 15,
            'moneda' => 'C$',
            'activo' => true,
        ]);
    }

    /**
     * Obtener el nombre de la tienda
     */
    public static function getNombre()
    {
        return self::getConfiguracion()->nombre;
    }

    /**
     * Obtener el porcentaje de impuesto
     */
    public static function getImpuesto()
    {
        return self::getConfiguracion()->impuesto;
    }

    /**
     * Obtener la moneda
     */
    public static function getMoneda()
    {
        return self::getConfiguracion()->moneda;
    }

    /**
     * Obtener el teléfono
     */
    public static function getTelefono()
    {
        return self::getConfiguracion()->telefono;
    }

    /**
     * Obtener la dirección
     */
    public static function getDireccion()
    {
        return self::getConfiguracion()->direccion;
    }

    /**
     * Obtener el mensaje del ticket
     */
    public static function getMensajeTicket()
    {
        return self::getConfiguracion()->mensaje_ticket;
    }

    /**
     * Obtener el logo
     */
    public static function getLogo()
    {
        return self::getConfiguracion()->logo;
    }

    /**
     * Verificar si la tienda está activa
     */
    public static function estaActiva()
    {
        return self::getConfiguracion()->activo;
    }

    /**
     * Calcular el impuesto de un monto
     */
    public static function calcularImpuesto($monto)
    {
        $impuesto = self::getImpuesto();
        return ($monto * $impuesto) / 100;
    }

    /**
     * Calcular el total con impuesto
     */
    public static function calcularTotalConImpuesto($subtotal)
    {
        $impuesto = self::calcularImpuesto($subtotal);
        return $subtotal + $impuesto;
    }

    /**
     * Formatear monto con la moneda
     */
    public static function formatearMonto($monto)
    {
        $moneda = self::getMoneda();
        return $moneda . ' ' . number_format($monto, 2);
    }

    /**
     * Obtener la URL del logo
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    /**
     * Obtener la información completa de la tienda
     */
    public function getInformacionCompletaAttribute()
    {
        return [
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'impuesto' => $this->impuesto,
            'moneda' => $this->moneda,
            'mensaje_ticket' => $this->mensaje_ticket,
            'logo_url' => $this->logo_url,
        ];
    }

    /**
     * Actualizar la configuración
     */
    public static function actualizar($data)
    {
        $configuracion = self::getConfiguracion();
        $configuracion->update($data);
        return $configuracion;
    }

    /**
     * Resetear la configuración a valores por defecto
     */
    public static function resetear()
    {
        $configuracion = self::getConfiguracion();
        $configuracion->update([
            'nombre' => 'Pulpería Managua',
            'impuesto' => 15,
            'moneda' => 'C$',
            'telefono' => null,
            'direccion' => null,
            'mensaje_ticket' => null,
            'logo' => null,
            'activo' => true,
        ]);
        return $configuracion;
    }
}
