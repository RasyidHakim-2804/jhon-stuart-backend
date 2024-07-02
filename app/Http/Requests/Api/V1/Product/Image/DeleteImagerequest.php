<?php

namespace App\Http\Requests\Api\V1\Product\Image;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Image\ProductImage;

class DeleteImagerequest extends FormRequest
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
            'image_id' => [
                'required',
                Rule::exists(ProductImage::class, 'id')->where(function($query){
                    $query->where('product_id', $this->route()->parameters()['product']->id);
                })
            ]
        ];
    }
}
