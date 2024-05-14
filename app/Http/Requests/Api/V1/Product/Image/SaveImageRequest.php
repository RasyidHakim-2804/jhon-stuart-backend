<?php

namespace App\Http\Requests\Api\V1\Product\Image;

use App\Models\Image\ProductImage;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsArray;
use Illuminate\Validation\Rule;

class SaveImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages()
    {
        return [
            'images_id.*.exists' => 'Id is not exist in product_image or this image does not belong to this product.'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isArray = new IsArray;
        return [
            'images_id'      => [ $isArray ],
            'images_id.*'    => [
                'required',
                'numeric',
                'distinct',
                'min:1',
                Rule::exists(ProductImage::class, 'id')->where(function($query){
                    $query->where('product_id', $this->route()->parameters()['product']->id);
                })
            ],
        ];
    }
}
