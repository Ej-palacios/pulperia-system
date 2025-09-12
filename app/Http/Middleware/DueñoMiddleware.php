<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DueñoMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Solo dueños pueden acceder
        if (!auth()->user()->hasRole('dueño')) {
            abort(403, 'Acceso restringido. Se requieren permisos de dueño.');
        }

        return $next($request);
    }
}