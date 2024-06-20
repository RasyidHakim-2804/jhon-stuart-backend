<?php

namespace App\Http\Requests\Api\V1\Article;

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
        return [
            'title'          => 'required|string|max:255',
            
            'body.en'        => 'required|string',
            'body.id'        => 'required|string',

            'author_id'      => 'required|exists:article_authors,id',
            'category_id'    => 'required|string|exists:article_categories,id',

            'image'          => 'required|image|max:2048'
        ];
    }
}
