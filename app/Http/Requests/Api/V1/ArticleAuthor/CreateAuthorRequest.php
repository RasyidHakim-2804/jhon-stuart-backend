<?php

namespace App\Http\Requests\Api\V1\ArticleAuthor;

use Illuminate\Foundation\Http\FormRequest;

class CreateAuthorRequest extends FormRequest
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
            'image'         => 'required|image|max:2048',
            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
        ];
    }
}
