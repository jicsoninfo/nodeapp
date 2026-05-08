<?php
namespace App\Http\Requests\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'product.category_id'              => 'sometimes|uuid|exists:categories,id',
            'product.brand_id'                 => 'nullable|uuid|exists:brands,id',
            'product.status'                   => 'sometimes|in:draft,active,inactive,archived',
            'translations'                     => 'sometimes|array|min:1',
            'translations.*.lang_code'         => 'required_with:translations|exists:languages,code',
            'translations.*.name'              => 'required_with:translations|string|max:500',
            'translations.*.description'       => 'nullable|string',
            'translations.*.short_description' => 'nullable|string|max:500',
            'translations.*.meta_title'        => 'nullable|string|max:255',
            'translations.*.meta_description'  => 'nullable|string|max:500',
        ];
    }
}
