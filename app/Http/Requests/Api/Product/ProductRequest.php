<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $rules = [
            'name'                  => 'required|max:100',
            'meta'                  => 'required|string',
            'description'           => 'required|string',
            'is_varied'             => 'required|boolean',
            'is_active'             => 'required|boolean',
            'code'                  => 'required|string|unique:products,code',
            'type'                  => [
                'required',
                Rule::in([
                    ProductCategory::MUSEUM_TYPE,
                    ProductCategory::GALLERY_TYPE
                    ])
                ],
            'category_id'           => [
                'required   ',
                Rule::exists('product_categories', 'id')->where('type', $this->input('type'))
            ],
            'price'                 => 'exclude_if:is_varied,true|required|numeric|max:10000000',
            'usd_price'             => 'exclude_if:is_varied,true|required|numeric|max:10000000',

            'variants'              => 'exclude_if:is_varied,false|required|array|max:5',
            'variants.*.name'       => ['exclude_if:is_varied,false', 'required', Rule::in(ProductVariant::NAME), 'distinct'],
            'variants.*.price'      => ['exclude_if:is_varied,false', 'required', 'numeric', 'max:10000000'],
            'variants.*.usd_price'  => ['exclude_if:is_varied,false', 'required', 'numeric', 'max:10000000'],
            'variants.*.stock'      => ['exclude_if:is_varied,false', 'required', 'numeric','min:0', 'max:10000000'],
            'variants.*.is_active'  => ['exclude_if:is_varied,false', 'required', 'boolean'],
            'variants.*.code'       => [
                'exclude_if:is_varied,false',
                'required',
                'distinct',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    $variantId = $this->input(str_replace('code', 'id', $attribute)); // variants.*.code => variants.*.id

                    if ($variantId) { //for update

                        $exists    = DB::table('product_variants')
                                    ->where('code', '=', $value)
                                    ->where('id', '!=', $variantId)
                                    ->where('deleted_at', null)
                                    ->exists();

                    } else { //for store

                        $exists    = DB::table('product_variants')
                                    ->where('code', '=', $value)
                                    ->where('deleted_at', null)
                                    ->exists();
                    }

                    if ($exists) {

                        $fail('The ' . $attribute . ' has beed already taken');
                    }
                },
            ],
        ];

        if (strtolower($this->method()) == 'put') {
            $rules['variants.*.id'] = [
                'exclude_if:is_varied,false',
                'nullable',
                'distinct',
                Rule::exists('product_variants', 'id')
                    ->where('product_id', $this->route('product')->id)
                    ->where('deleted_at', '=', null)
            ];

            $rules['code'] = [
                'required',
                'string',
                Rule::unique('products', 'code')->ignore($this->route('product')->id, 'id')
            ];
        }

        if ($this->input('category_type') == ProductCategory::MUSEUM_TYPE) {
            $rules['is_varied'] == [
                function($attribute, $value, $fail) {
                    if ($value == 1) {
                        $fail('Musseum type product cannot have variants');
                    }
                }
            ];
        }

        return $rules;
    }
}
