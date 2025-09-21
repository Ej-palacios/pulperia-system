<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar funciones globales de helper
        if (!function_exists('pulperia_format_currency')) {
            function pulperia_format_currency($amount, $decimals = 2) {
                return \App\Helpers\PulperiaHelper::formatCurrency($amount, $decimals);
            }
        }

        if (!function_exists('pulperia_format_date')) {
            function pulperia_format_date($date, $format = 'd/m/Y') {
                return \App\Helpers\PulperiaHelper::formatDate($date, $format);
            }
        }

        if (!function_exists('pulperia_format_datetime')) {
            function pulperia_format_datetime($date, $format = 'd/m/Y H:i') {
                return \App\Helpers\PulperiaHelper::formatDateTime($date, $format);
            }
        }

        if (!function_exists('pulperia_format_number')) {
            function pulperia_format_number($number, $decimals = 0) {
                return \App\Helpers\PulperiaHelper::formatNumber($number, $decimals);
            }
        }

        // Registrar directivas de Blade personalizadas
        Blade::directive('currency', function ($expression) {
            return "<?php echo \\App\\Helpers\\PulperiaHelper::formatCurrency($expression); ?>";
        });

        Blade::directive('date', function ($expression) {
            return "<?php echo \\App\\Helpers\\PulperiaHelper::formatDate($expression); ?>";
        });

        Blade::directive('datetime', function ($expression) {
            return "<?php echo \\App\\Helpers\\PulperiaHelper::formatDateTime($expression); ?>";
        });

        Blade::directive('number', function ($expression) {
            return "<?php echo \\App\\Helpers\\PulperiaHelper::formatNumber($expression); ?>";
        });
    }
}

