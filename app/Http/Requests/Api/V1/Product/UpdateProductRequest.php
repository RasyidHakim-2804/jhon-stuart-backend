<?php

namespace App\Http\Requests\Api\V1\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Query\Builder;
use App\Models\ProductVariant;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
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

    protected function prepareForValidation()
    {
        if ($this->input('category_type') == ProductCategory::MUSEUM_TYPE) {
            $this->merge(['is_varied' => 0]);
        }
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
            'short_description'     => 'required|string',
            'full_description'      => 'required|string',
            'is_varied'             => 'required|boolean',
            'available'             => 'required|boolean',  
            'category_type'         => [
                'required',
                Rule::in([
                    ProductCategory::MUSEUM_TYPE,
                    ProductCategory::GALLERY_TYPE
                    ])
                ],
            'category_id'           => [
                'required   ',
                Rule::exists('product_categories', 'id')->where('type', $this->input('category_type'))
            ],
            'idr_price'             => 'exclude_if:is_varied,true|required|numeric|max:10000000',
            'usd_price'             => 'exclude_if:is_varied,true|required|numeric|max:10000000',
            'stock'                 => 'exclude_if:is_varied,true|required|numeric|min:0',
            'code'                  => [
                'exclude_if:is_varied,true',
                'required',
                'string',
                Rule::unique('product_variants', 'code')->ignore($this->route('product')->id, 'product_id')
            ],

            'variants'              => 'exclude_if:is_varied,false|required|array|max:5',
            'variants.*.type'       => ['exclude_if:is_varied,false', 'required', Rule::in(ProductVariant::VARIANT_TYPE), 'distinct'],
            'variants.*.idr_price'  => ['exclude_if:is_varied,false', 'required', 'numeric', 'max:10000000'],
            'variants.*.usd_price'  => ['exclude_if:is_varied,false', 'required', 'numeric', 'max:10000000'],
            'variants.*.stock'      => ['exclude_if:is_varied,false', 'required', 'numeric','min:0', 'max:10000000'],
            'variants.*.available'  => ['exclude_if:is_varied,false', 'required', 'boolean'],  
            'variants.*.id'         => [
                'exclude_if:is_varied,false',
                'nullable',
                'distinct',
                Rule::exists('product_variants', 'id')->where('product_id', $this->route('product')->id)
            ],
            'variants.*.code'       => [
                'exclude_if:is_varied,false', 
                'required', 
                'string', 
                'max:100',
                function ($attribute, $value, $fail) {
                    $variantId = $this->input(str_replace('code', 'id', $attribute));
                    
                    if ($variantId) {//old variant
                        $exists    = DB::table('product_variants')->where('id', $variantId)->where('code', '!=', $value)->exists();
                    } else {//new variant
                        $exists    = DB::table('product_variants')->where('code', '=', $value)->exists();
                    }
    
                    if ($exists) {
                        $fail('The ' . $attribute . ' has beed already taken');
                    }
                },
            ],
        ];
    }
}
