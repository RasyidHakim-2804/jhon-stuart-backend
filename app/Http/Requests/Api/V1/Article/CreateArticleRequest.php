<?php

namespace App\Http\Requests\Api\V1\Article;

use App\Rules\IsArray;
use Illuminate\Foundation\Http\FormRequest;

class CreateArticleRequest extends FormRequest
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
            'title'    => ['required', $isArray],
            'title.en' => ['required', 'string', 'min:3', 'max:255'],
            'title.id' => ['required', 'string', 'min:3', 'max:255'],

            'body'     => ['required', $isArray],
            'body.en'  => ['required', 'string', 'min:3', 'max:255'],
            'body.id'  => ['required', 'string', 'min:3', 'max:255'],

            'image'    => ['required', 'image','max:2048']
        ];
    }
}
