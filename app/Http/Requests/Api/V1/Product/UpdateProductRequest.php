<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use App\Models\ProductCategory;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'categories.*.exists' => 'Category Id is not exists or id is not ' . $this->input('category_type') . ' type',
            'variants.*.id'       => 'Id is not exist in variants or this variant does not belong to this product.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                  => 'required|max:100',

            'description.id'        => 'required|string',
            'description.en'        => 'required|string',
            
            'category_type'         => [
                'required',
                'string',
                Rule::in(ProductCategory::$categoryTypes)
            ],
            'categories.*'          => [
                'numeric',
                'distinct',
                Rule::exists('product_categories', 'id')->where(function(Builder $query) {
                    return $query->where('type', $this->input('category_type'));
                })
            ],
            
            'is_varied'             => 'required|boolean',
            'is_saleable'           => 'required|boolean',

            'size'                  => 'exclude_if:is_varied,true|required|string|max:255',
            'idr_price'             => 'exclude_if:is_varied,true|required|numeric|max:10000000',
            'usd_price'             => 'exclude_if:is_varied,true|required|numeric|max:10000000',
            'stock'                 => 'exclude_if:is_varied,true|required|numeric|min:0',
            
            'variants.*.name'       => 'exclude_if:is_varied,false|required|max:100',
            'variants.*.idr_price'  => 'exclude_if:is_varied,false|required|numeric|max:10000000',
            'variants.*.usd_price'  => 'exclude_if:is_varied,false|required|numeric|max:10000000',
            'variants.*.size'       => 'exclude_if:is_varied,false|required|string|max:255',
            'variants.*.stock'      => 'exclude_if:is_varied,false|required|numeric|min:0|max:10000000',
            'variants.*.id'         => [
                'exclude_if:is_varied,false',
                'nullable',
                'distinct',
                Rule::exists('product_variants', 'id')->where(function($query){
                    $query->where('product_id', $this->route()->parameters()['product']->id);
                })
            ],
        ];
    }
}
