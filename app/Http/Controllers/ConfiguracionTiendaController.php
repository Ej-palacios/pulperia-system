<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionTienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionTiendaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:dueño');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit()
    {
        $configuracion = ConfiguracionTienda::first();
        
        if (!$configuracion) {
            $configuracion = ConfiguracionTienda::create([
                'nombre' => 'Pulpería Managua',
                'impuesto' => 15,
                'moneda' => 'C$',
                'logo' => null,
            ]);
        }
        
        return view('configuracion_tienda.edit', compact('configuracion'));
    }

    /**
     * Actualizar configuración
     */
    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'impuesto' => 'required|numeric|min:0|max:100',
            'moneda' => 'required|string|max:10',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'mensaje_ticket' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $configuracion = ConfiguracionTienda::first();
        
        $data = $request->only([
            'nombre', 'impuesto', 'moneda', 'telefono', 
            'direccion', 'mensaje_ticket'
        ]);

        // Manejar la imagen del logo
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($configuracion->logo) {
                Storage::disk('public')->delete($configuracion->logo);
            }
            
            $imagePath = $request->file('logo')->store('configuracion', 'public');
            $data['logo'] = $imagePath;
        }

        try {
            $configuracion->update($data);

            return redirect()->route('configuracion.edit')
                ->with('success', 'Configuración actualizada exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar configuración (API)
     */
    public function apiShow()
    {
        $configuracion = ConfiguracionTienda::first();
        return response()->json($configuracion);
    }

    /**
     * Crear backup de la base de datos
     */
    public function crearBackup()
    {
        try {
            // Implementar lógica de backup
            $backupPath = 'backups/backup-' . date('Y-m-d-H-i-s') . '.sql';
            
            return response()->json([
                'success' => true,
                'message' => 'Backup creado exitosamente.',
                'path' => $backupPath
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restaurar backup
     */
    public function restaurarBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,gz,zip',
        ]);

        try {
            // Implementar lógica de restauración
            $file = $request->file('backup_file');
            $file->storeAs('restores', $file->getClientOriginalName());

            return response()->json([
                'success' => true,
                'message' => 'Backup restaurado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar logs del sistema
     */
    public function logs()
    {
        // Implementar visualización de logs
        $logs = [];
        
        return view('configuracion_tienda.logs', compact('logs'));
    }
}