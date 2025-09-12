<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:proveedores,email',
            'direccion' => 'nullable|string|max:500',
            'notas' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ];
    }
}