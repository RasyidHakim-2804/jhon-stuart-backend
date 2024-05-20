<?php

namespace App\Http\Requests\Api\V1\Product;

use App\Rules\IsArray;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'categories.*.exists' => 'Id is not exist in categories',
            'variants.min'        => 'The variants field must be at least 2 objects'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isArray    = new IsArray;

        return [
            'name'                  => 'required|min:3|max:100',
            'description'           => 'required|min:3|max:1000',
            'categories'            => ['required', $isArray],
            'categories.*'          => 'numeric|exists:categories,id|distinct',
            'is_varied'             => 'required|boolean',
            'idr_price'             => 'exclude_if:is_varied,true|required|numeric|min:1|max_digits:8',
            'usd_price'             => 'exclude_if:is_varied,true|required|numeric|min:1|max_digits:8',
            'stock'                 => 'exclude_if:is_varied,true|required|numeric|min:0|max_digits:5',
            'min_order'             => 'exclude_if:is_varied,true|required|numeric|min:1|max_digits:8`',
            'max_order'             => 'exclude_if:is_varied,true|nullable|numeric|max_digits:8|gte:min_order',
            'variants'              => ['exclude_if:is_varied,false', 'required', $isArray, 'min:2'],
            'variants.*.name'       => ['exclude_if:is_varied,false', 'required', 'min:3', 'max:100',],
            'variants.*.idr_price'  => ['exclude_if:is_varied,false', 'required', 'numeric','min:1', 'max:10000000','max_digits:8'],
            'variants.*.usd_price'  => ['exclude_if:is_varied,false', 'required', 'numeric','min:1', 'max:10000000','max_digits:8'],
            'variants.*.min_order'  => ['exclude_if:is_varied,false', 'required', 'numeric','min:1', 'max:10000000','max_digits:8'],
            'variants.*.max_order'  => ['exclude_if:is_varied,false', 'nullable', 'numeric','max_digits:8','gte:variants.*.min_order'],
            'variants.*.stock'      => ['exclude_if:is_varied,false', 'required', 'numeric','min:0', 'max:10000000','max_digits:8']
        ];
    }
}
