<?php

namespace App\Http\Requests\Api\Article;

use App\Rules\IsArray;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'meta'        => 'required|string|max:255',
            'author_id'   => 'required|exists:article_authors,id',
            'category_id' => 'required|string|exists:article_categories,id',
            'image'       => 'nullable|image|max:2048'
        ];
    }
}
