<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Muestra el formulario de registro
     */
    public function showRegistrationForm()
    {
        $adminExists = User::role('administrador')->exists() || User::role('dueño')->exists();
        return view('auth.register', compact('adminExists'));
    }

    /**
     * Procesa el registro de un nuevo usuario
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string'],
        ]);

        // Permitir un único admin/dueño
        if (in_array($request->role, ['administrador','dueño'])) {
            $exists = User::role(['administrador','dueño'])->exists();
            if ($exists) {
                return back()->withErrors(['role' => 'Ya existe un usuario Administrador/Dueño.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'username' => $request->username,
            // El modelo castea 'password' => 'hashed', así que no usar Hash::make
            'password' => $request->password,
        ]);

        // Asignar rol seleccionado
        $user->syncRoles([$request->role]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Usuario registrado exitosamente.');
    }
}
