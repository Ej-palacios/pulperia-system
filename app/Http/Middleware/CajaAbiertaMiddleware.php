<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ArqueoCaja;
use Carbon\Carbon;

class CajaAbiertaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si hay un arqueo de caja para el día actual
        $arqueoHoy = ArqueoCaja::whereDate('created_at', Carbon::today())->first();

        // Si no hay arqueo hoy, la caja está abierta
        if (!$arqueoHoy) {
            return $next($request);
        }

        // Si ya se hizo arqueo de caja hoy, redirigir con mensaje
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'La caja ya fue cerrada hoy. No se pueden realizar más ventas.'
            ], 403);
        }

        return redirect()->back()
            ->with('error', 'La caja ya fue cerrada hoy. No se pueden realizar más ventas.');
    }
}