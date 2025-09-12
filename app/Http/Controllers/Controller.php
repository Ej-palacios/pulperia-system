<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    /**
     * Respuesta JSON estándar para éxito
     */
    protected function jsonSuccess($data = null, $message = 'Operación exitosa', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
    
    /**
     * Respuesta JSON estándar para errores
     */
    protected function jsonError($message = 'Error en la operación', $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
    
    /**
     * Redirección con mensaje de éxito
     */
    protected function redirectSuccess($route, $message = 'Operación exitosa')
    {
        return redirect()->route($route)->with('success', $message);
    }
    
    /**
     * Redirección con mensaje de error
     */
    protected function redirectError($route, $message = 'Error en la operación')
    {
        return redirect()->route($route)->with('error', $message);
    }
    
    /**
     * Redirección back con mensaje de éxito
     */
    protected function backSuccess($message = 'Operación exitosa')
    {
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Redirección back con mensaje de error
     */
    protected function backError($message = 'Error en la operación')
    {
        return redirect()->back()->with('error', $message);
    }
    
    /**
     * Validar si el usuario tiene un rol específico
     */
    protected function userHasRole($role)
    {
        return auth()->check() && auth()->user()->hasRole($role);
    }
    
    /**
     * Validar si el usuario tiene alguno de los roles especificados
     */
    protected function userHasAnyRole($roles)
    {
        return auth()->check() && auth()->user()->hasAnyRole($roles);
    }
    
    /**
     * Validar si el usuario tiene todos los roles especificados
     */
    protected function userHasAllRoles($roles)
    {
        return auth()->check() && auth()->user()->hasAllRoles($roles);
    }
    
    /**
     * Formatear moneda
     */
    protected function formatCurrency($amount)
    {
        return 'C$ ' . number_format($amount, 2);
    }
    
    /**
     * Formatear fecha
     */
    protected function formatDate($date)
    {
        return $date->format('d/m/Y');
    }
    
    /**
     * Formatear fecha y hora
     */
    protected function formatDateTime($date)
    {
        return $date->format('d/m/Y H:i:s');
    }
}