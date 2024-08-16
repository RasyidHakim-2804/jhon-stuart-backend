<?php

namespace App\Http\Requests\Api\ProductImage;

use App\Models\ProductImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductImageRequest extends FormRequest
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
            'images_id'      => 'array',
            'images_id.*'    => [
                'required',
                'numeric',
                'distinct',
                'min:1',
                Rule::exists(ProductImage::class, 'id')->where(function($query){
                    $query->where('product_id', $this->route('product')->id);
                })
            ],
        ];
    }
}
