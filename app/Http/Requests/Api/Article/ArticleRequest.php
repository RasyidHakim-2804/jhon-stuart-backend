<?php

namespace App\Http\Requests\Api\Article;

use App\Models\Article;
use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'title'         => 'array',
            'meta'          => 'array',
            'body'          => 'array',
            'type'          => [
                'required',
                Rule::in(Article::TYPE),
            ],
            'category_id'   => 'required|exists:article_categories,id',
            'thumbnail'     => 'required|image|mimes:jpeg,png,jpg,svg|max:2000',
        ];

        foreach (Language::code() as $language) {
            $rules['title.' . $language] = 'required|string';
            $rules['meta.' . $language]  = 'required|string';
            $rules['body.' . $language]  = 'required|string';
        }

        if (strtolower($this->method()) == 'put') { // for update
            $rules['thumbnail'] = 'nullable|image|mimes:jpeg,png,jpg,svg|max:2000';
        }

        return $rules;
    }
}
