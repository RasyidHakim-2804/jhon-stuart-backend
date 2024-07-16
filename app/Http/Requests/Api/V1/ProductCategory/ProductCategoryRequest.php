<?php

namespace App\Http\Requests\Api\V1\ProductCategory;

use App\Models\ProductCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:30',
            'id_description' => 'required|string|max:255',
            'en_description' => 'required|string|max:255',
            'type' => [
                'required', 
                'string',
                Rule::in([
                    ProductCategory::MUSEUM_TYPE,
                    ProductCategory::GALLERY_TYPE
                ])
            ]
        ];
    }
}
