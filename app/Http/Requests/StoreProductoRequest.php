<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre'        => ['required','string','max:255'],
            'sku'           => ['nullable','string','max:50'],
            'precio'        => ['required','numeric','min:0.01'],
            'unidad'        => ['required','string','max:20'],
            'stock'         => ['required','integer','min:0'],
            'stock_minimo'  => ['required','integer','min:0'],
            'ubicacion'     => ['nullable','string','max:255'],
            'lote'          => ['nullable','string','max:50'],
            'fecha_caducidad' => ['nullable','date'],
            'categoria_id'  => ['required','integer','exists:categorias,id'],
            'proveedor_id'  => ['required','integer','exists:proveedores,id'],
        ];
    }
}