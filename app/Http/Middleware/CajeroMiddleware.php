<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CajeroMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cajeros, administradores y dueños pueden acceder
        if (!auth()->user()->hasRole('cajero|administrador|dueño')) {
            abort(403, 'Acceso restringido. Se requieren permisos de cajero.');
        }

        return $next($request);
    }
}