<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Solo dueños y administradores pueden acceder
        if (!auth()->user()->hasRole('dueño|administrador')) {
            abort(403, 'Acceso restringido. Se requieren permisos de administrador.');
        }

        return $next($request);
    }
}