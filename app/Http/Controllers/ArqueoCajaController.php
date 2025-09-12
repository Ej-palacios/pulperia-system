<?php

namespace App\Http\Controllers;

use App\Models\ArqueoCaja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArqueoCajaController extends Controller
{
    /**
     * Listar todos los arqueos de caja
     */
    public function index()
    {
        $arqueos = ArqueoCaja::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(30);
            
        return view('arqueo_caja.index', compact('arqueos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        // Verificar si ya existe un arqueo para el día actual
        $arqueoHoy = ArqueoCaja::whereDate('created_at', Carbon::today())->first();
        
        if ($arqueoHoy) {
            return redirect()->route('arqueo-caja.show', $arqueoHoy->id)
                ->with('info', 'Ya se realizó el arqueo de caja para hoy.');
        }

        // Obtener ventas del día
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())
            ->where('tipo_pago', 'contado')
            ->get();
            
        $totalVentasContado = $ventasHoy->sum('total');
        
        return view('arqueo_caja.create', compact('totalVentasContado'));
    }

    /**
     * Almacenar nuevo arqueo de caja
     */
    public function store(Request $request)
    {
        $request->validate([
            'efectivo_inicial' => 'required|numeric|min:0',
            'efectivo_final' => 'required|numeric|min:0',
            'ventas_contado' => 'required|numeric|min:0',
            'otros_ingresos' => 'nullable|numeric|min:0',
            'otros_gastos' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Verificar si ya existe un arqueo para hoy
        $arqueoHoy = ArqueoCaja::whereDate('created_at', Carbon::today())->first();
        if ($arqueoHoy) {
            return redirect()->back()
                ->with('error', 'Ya se realizó un arqueo de caja para hoy.');
        }

        DB::beginTransaction();

        try {
            // Calcular diferencia
            $total_teorico = $request->efectivo_inicial + $request->ventas_contado + 
                            $request->otros_ingresos - $request->otros_gastos;
            $diferencia = $request->efectivo_final - $total_teorico;

            // Crear arqueo
            $arqueo = ArqueoCaja::create([
                'user_id' => auth()->id(),
                'efectivo_inicial' => $request->efectivo_inicial,
                'efectivo_final' => $request->efectivo_final,
                'ventas_contado' => $request->ventas_contado,
                'otros_ingresos' => $request->otros_ingresos ?? 0,
                'otros_gastos' => $request->otros_gastos ?? 0,
                'total_teorico' => $total_teorico,
                'diferencia' => $diferencia,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('arqueo-caja.show', $arqueo->id)
                ->with('success', 'Arqueo de caja registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar el arqueo de caja: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles del arqueo de caja
     */
    public function show(ArqueoCaja $arqueoCaja)
    {
        $arqueoCaja->load('user');
        return view('arqueo_caja.show', compact('arqueoCaja'));
    }

    /**
     * Generar reporte del arqueo de caja
     */
    public function generarReporte(ArqueoCaja $arqueoCaja)
    {
        $arqueoCaja->load('user');
        
        // Implementar generación de PDF
        return response()->json([
            'success' => true,
            'arqueo' => $arqueoCaja
        ]);
    }

    /**
     * Cierre rápido de caja
     */
    public function cierreRapido(Request $request)
    {
        $request->validate([
            'efectivo_final' => 'required|numeric|min:0',
        ]);

        // Verificar si ya existe un arqueo para hoy
        $arqueoHoy = ArqueoCaja::whereDate('created_at', Carbon::today())->first();
        if ($arqueoHoy) {
            return response()->json([
                'success' => false,
                'message' => 'Ya se realizó un arqueo de caja para hoy.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Obtener ventas del día
            $ventasHoy = Venta::whereDate('created_at', Carbon::today())
                ->where('tipo_pago', 'contado')
                ->get();
                
            $totalVentasContado = $ventasHoy->sum('total');

            // Usar efectivo inicial de la configuración o calcular
            $efectivoInicial = 0; // Esto debería venir de la configuración

            // Calcular diferencia
            $total_teorico = $efectivoInicial + $totalVentasContado;
            $diferencia = $request->efectivo_final - $total_teorico;

            // Crear arqueo
            $arqueo = ArqueoCaja::create([
                'user_id' => auth()->id(),
                'efectivo_inicial' => $efectivoInicial,
                'efectivo_final' => $request->efectivo_final,
                'ventas_contado' => $totalVentasContado,
                'total_teorico' => $total_teorico,
                'diferencia' => $diferencia,
                'observaciones' => 'Cierre rápido automático',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cierre rápido realizado exitosamente.',
                'arqueo_id' => $arqueo->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en el cierre rápido: ' . $e->getMessage()
            ], 500);
        }
    }
}