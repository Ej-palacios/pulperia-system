<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:20|unique:clientes,cedula',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->cedula && !$this->validarCedulaNicaragua($this->cedula)) {
                $validator->errors()->add('cedula', 'La cédula no tiene un formato válido para Nicaragua.');
            }
        });
    }

    private function validarCedulaNicaragua($cedula)
    {
        // Validación básica de cédula nicaragüense
        return preg_match('/^[0-9]{3}-[0-9]{6}-[0-9]{4}[A-Z]$/', $cedula) ||
               preg_match('/^[0-9]{14}$/', $cedula);
    }
}