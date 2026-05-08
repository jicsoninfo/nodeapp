<?php
namespace App\Http\Requests\Vendor;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'store_name'              => 'sometimes|string|max:200|unique:vendors,store_name,' . $this->user()->vendor?->id,
            'profile.description'    => 'nullable|string|max:2000',
            'profile.business_type'  => 'nullable|in:individual,company,brand',
            'profile.tax_id'         => 'nullable|string|max:100',
            'profile.website_url'    => 'nullable|url|max:500',
            'translations'           => 'nullable|array',
            'translations.*.lang_code'   => 'required_with:translations|exists:languages,code',
            'translations.*.description' => 'nullable|string|max:2000',
        ];
    }
}
