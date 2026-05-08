<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product.category_id'                   => 'required|uuid|exists:categories,id',
            'product.brand_id'                      => 'nullable|uuid|exists:brands,id',
            'product.status'                        => 'required|in:draft,active',

            'translations'                          => 'required|array|min:1',
            'translations.*.lang_code'              => 'required|string|exists:languages,code',
            'translations.*.name'                   => 'required|string|max:500',
            'translations.*.description'            => 'nullable|string',
            'translations.*.short_description'      => 'nullable|string|max:500',
            'translations.*.meta_title'             => 'nullable|string|max:255',
            'translations.*.meta_description'       => 'nullable|string|max:500',

            'variants'                              => 'required|array|min:1',
            'variants.*.data.sku'                   => 'required|string|unique:product_variants,sku',
            'variants.*.data.price'                 => 'required|numeric|min:0',
            'variants.*.data.sale_price'            => 'nullable|numeric|lt:variants.*.data.price',
            'variants.*.data.currency'              => 'required|string|size:3',
            'variants.*.data.stock_quantity'        => 'required|integer|min:0',
            'variants.*.data.weight_grams'          => 'nullable|integer|min:0',
            'variants.*.attribute_values'           => 'nullable|array',
            'variants.*.attribute_values.*'         => 'uuid|exists:attribute_values,id',
        ];
    }
}
