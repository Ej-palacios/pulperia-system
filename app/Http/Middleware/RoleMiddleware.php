<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role, $permission = null)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Verificar si el usuario tiene el rol requerido
        if (!$user->hasRole($role)) {
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        // Verificar permisos adicionales si se especifican
        if ($permission !== null && !$user->can($permission)) {
            abort(403, 'No tiene permisos para realizar esta acción.');
        }

        return $next($request);
    }
}