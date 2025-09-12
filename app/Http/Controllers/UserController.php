<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:dueño|administrador');
    }

    /**
     * Listar todos los usuarios
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('usuarios.index', compact('users'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Almacenar nuevo usuario
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'activo' => $request->activo,
            ]);

            $user->assignRole($request->role);

            DB::commit();

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de usuario
     */
    public function show(User $user)
    {
        return view('usuarios.show', compact('user'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('user', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->direccion = $request->direccion;
            $user->activo = $request->activo;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Sincronizar roles
            $user->syncRoles([$request->role]);

            DB::commit();

            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        try {
            $user->delete();
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar estado del usuario (API)
     */
    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'activo' => 'required|boolean',
        ]);

        try {
            $user->activo = $request->activo;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado'
            ], 500);
        }
    }
}