<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockDisponibleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Solo aplicar a rutas de ventas
        if (!$request->is('ventas*') && !$request->is('api/ventas*')) {
            return $next($request);
        }

        // Validar stock para solicitudes que contienen productos
        if ($request->has('productos')) {
            foreach ($request->productos as $producto) {
                $productoModel = \App\Models\Producto::find($producto['id']);
                
                if (!$productoModel || $productoModel->stock < $producto['cantidad']) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'error' => 'Stock insuficiente para ' . ($productoModel->nombre ?? 'producto'),
                            'stock_disponible' => $productoModel->stock ?? 0
                        ], 422);
                    }

                    return redirect()->back()
                        ->with('error', 'Stock insuficiente para ' . ($productoModel->nombre ?? 'producto'));
                }
            }
        }

        return $next($request);
    }
}