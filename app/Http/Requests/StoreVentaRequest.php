<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|numeric|min:0.1',
            'productos.*.precio' => 'required|numeric|min:0.01',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'tipo_pago' => 'required|in:contado,credito',
        ];
    }

    public function messages()
    {
        return [
            'cliente_id.exists' => 'El cliente seleccionado no es válido.',
            'productos.required' => 'Debe agregar al menos un producto a la venta.',
            'productos.*.id.exists' => 'Uno o más productos no son válidos.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria para todos los productos.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 0.1.',
            'productos.*.precio.required' => 'El precio es obligatorio para todos los productos.',
            'productos.*.precio.min' => 'El precio debe ser al menos 0.01.',
            'subtotal.required' => 'El subtotal es obligatorio.',
            'impuestos.required' => 'Los impuestos son obligatorios.',
            'total.required' => 'El total es obligatorio.',
            'tipo_pago.required' => 'El tipo de pago es obligatorio.',
            'tipo_pago.in' => 'El tipo de pago debe ser contado o crédito.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que haya suficiente stock para cada producto
            foreach ($this->productos as $index => $producto) {
                $productoModel = \App\Models\Producto::find($producto['id']);
                
                if ($productoModel && $productoModel->stock < $producto['cantidad']) {
                    $validator->errors()->add(
                        "productos.$index.cantidad",
                        "No hay suficiente stock de {$productoModel->nombre}. Stock disponible: {$productoModel->stock}"
                    );
                }
            }

            // Validar que si es crédito, se especifique un cliente
            if ($this->tipo_pago === 'credito' && empty($this->cliente_id)) {
                $validator->errors()->add('cliente_id', 'Debe seleccionar un cliente para ventas a crédito.');
            }

            // Validar que los cálculos sean correctos
            $subtotalCalculado = collect($this->productos)->sum(function ($producto) {
                return $producto['cantidad'] * $producto['precio'];
            });

            $impuestosCalculados = $subtotalCalculado * 0.15; // 15% de impuestos
            $totalCalculado = $subtotalCalculado + $impuestosCalculados;

            if (abs($subtotalCalculado - $this->subtotal) > 0.01) {
                $validator->errors()->add('subtotal', 'El subtotal calculado no coincide.');
            }

            if (abs($impuestosCalculados - $this->impuestos) > 0.01) {
                $validator->errors()->add('impuestos', 'Los impuestos calculados no coinciden.');
            }

            if (abs($totalCalculado - $this->total) > 0.01) {
                $validator->errors()->add('total', 'El total calculado no coincide.');
            }
        });
    }
}