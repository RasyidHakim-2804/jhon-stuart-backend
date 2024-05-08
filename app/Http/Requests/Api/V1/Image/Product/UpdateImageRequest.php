<?php

namespace App\Http\Requests\Api\V1\Image\Product;

use App\Rules\IsArray;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
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
        $isArray    = new IsArray;

        return [
            'images'    => ['required', ],
            'images.*'  => ['required', 'image','max:2048'],
        ];
    }
}
