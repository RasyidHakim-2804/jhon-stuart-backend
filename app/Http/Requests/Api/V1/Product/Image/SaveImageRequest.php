<?php

namespace App\Http\Requests\Api\V1\Product\Image;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsArray;

class SaveImageRequest extends FormRequest
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
        $isArray = new IsArray;
        return [
            'images_id'      => ['required', $isArray],
            'images_id.*'    => ['required', 'numeric', 'distinct', 'min:1', 'exists:product_images,id'],
        ];
    }
}
