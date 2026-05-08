<?php
namespace App\Http\Requests\Vendor;
use Illuminate\Foundation\Http\FormRequest;

class CreateVariantRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'sku'              => 'required|string|unique:product_variants,sku',
            'price'            => 'required|numeric|min:0',
            'sale_price'       => 'nullable|numeric|lt:price',
            'currency'         => 'required|string|size:3|exists:currencies,code',
            'stock_quantity'   => 'required|integer|min:0',
            'weight_grams'     => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
            'attribute_values' => 'nullable|array',
            'attribute_values.*' => 'uuid|exists:attribute_values,id',
        ];
    }
}
