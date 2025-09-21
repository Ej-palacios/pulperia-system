<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'categoria_id' => 'required|exists:categorias,id',
            'marca' => 'nullable|string|max:100',
            'unidad_compra' => 'required|string|max:20',
            'unidad_venta' => 'required|string|max:20',
            'factor_conversion' => 'required|numeric|min:0.001',
            'costo_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imagen_url' => 'nullable|url',
            'activo' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado.',
            'categoria_id.required' => 'La categoría es obligatoria.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'unidad_compra.required' => 'La unidad de compra es obligatoria.',
            'unidad_venta.required' => 'La unidad de venta es obligatoria.',
            'factor_conversion.required' => 'El factor de conversión es obligatorio.',
            'factor_conversion.min' => 'El factor de conversión debe ser mayor a 0.',
            'costo_compra.required' => 'El costo de compra es obligatorio.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'stock.required' => 'El stock es obligatorio.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'imagen.image' => 'El archivo debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser JPEG, PNG, JPG o GIF.',
            'imagen.max' => 'La imagen no debe pesar más de 2MB.',
            'imagen_url.url' => 'El enlace de imagen debe ser una URL válida.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->precio_venta <= $this->costo_compra) {
                $validator->errors()->add('precio_venta', 'El precio de venta debe ser mayor al costo de compra.');
            }
        });
    }
}