<?php

namespace App\Helpers;

class PulperiaHelper
{
    /**
     * Formatear moneda en córdobas nicaragüenses
     */
    public static function formatCurrency($amount, $decimals = 2)
    {
        return 'C$ ' . number_format($amount, $decimals);
    }

    /**
     * Formatear fecha en formato nicaragüense
     */
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format($format);
    }

    /**
     * Formatear fecha y hora en formato nicaragüense
     */
    public static function formatDateTime($date, $format = 'd/m/Y H:i')
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format($format);
    }

    /**
     * Validar cédula nicaragüense
     */
    public static function validateCedula($cedula)
    {
        $regex = '/^[0-9]{3}-[0-9]{6}-[0-9]{4}[A-Z]$|^[0-9]{14}$/';
        return preg_match($regex, $cedula);
    }

    /**
     * Validar teléfono nicaragüense
     */
    public static function validatePhone($phone)
    {
        $regex = '/^[+]?[0-9]{8,15}$/';
        return preg_match($regex, $phone);
    }

    /**
     * Generar código de barras único
     */
    public static function generateBarcode()
    {
        return 'PULP' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular impuestos (15% IVA)
     */
    public static function calculateTax($subtotal, $taxRate = 0.15)
    {
        return $subtotal * $taxRate;
    }

    /**
     * Calcular total con impuestos
     */
    public static function calculateTotal($subtotal, $taxRate = 0.15)
    {
        return $subtotal + self::calculateTax($subtotal, $taxRate);
    }

    /**
     * Formatear número con separadores de miles
     */
    public static function formatNumber($number, $decimals = 0)
    {
        return number_format($number, $decimals);
    }

    /**
     * Obtener el nombre del mes en español
     */
    public static function getMonthName($month)
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $months[$month] ?? '';
    }

    /**
     * Obtener el nombre del día en español
     */
    public static function getDayName($day)
    {
        $days = [
            0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
            4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
        ];
        
        return $days[$day] ?? '';
    }

    /**
     * Generar contraseña aleatoria
     */
    public static function generatePassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Limpiar string para uso en URLs
     */
    public static function slugify($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Obtener tiempo transcurrido en español
     */
    public static function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'Hace un momento';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return "Hace {$minutes} minuto" . ($minutes > 1 ? 's' : '');
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return "Hace {$hours} hora" . ($hours > 1 ? 's' : '');
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return "Hace {$days} día" . ($days > 1 ? 's' : '');
        } else {
            return self::formatDate($datetime);
        }
    }

    /**
     * Verificar si es horario de trabajo
     */
    public static function isBusinessHours()
    {
        $hour = (int) date('H');
        return $hour >= 8 && $hour < 18;
    }

    /**
     * Obtener estado del stock
     */
    public static function getStockStatus($current, $minimum)
    {
        if ($current <= 0) {
            return 'agotado';
        } elseif ($current <= $minimum) {
            return 'bajo';
        } elseif ($current <= $minimum * 2) {
            return 'medio';
        } else {
            return 'alto';
        }
    }

    /**
     * Obtener color del estado del stock
     */
    public static function getStockColor($status)
    {
        $colors = [
            'agotado' => 'error',
            'bajo' => 'warning',
            'medio' => 'info',
            'alto' => 'success'
        ];
        
        return $colors[$status] ?? 'info';
    }

    /**
     * Formatear tamaño de archivo
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Generar token único
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Sanitizar input
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Verificar si es fin de semana
     */
    public static function isWeekend($dayOfWeek = null)
    {
        $day = $dayOfWeek !== null ? (int) $dayOfWeek : (int) date('w');
        return $day === 0 || $day === 6;
    }

    /**
     * Obtener próximo día hábil
     */
    public static function getNextBusinessDay($date = null)
    {
        $date = $date ? new \DateTime($date) : new \DateTime();
        
        do {
            $date->add(new \DateInterval('P1D'));
        } while (self::isWeekend($date->format('w')));
        
        return $date;
    }
}

