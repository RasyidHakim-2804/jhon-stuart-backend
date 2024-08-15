<?php

namespace App\Http\Requests\Api\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'title'             => 'required|string|max:255',
            'body'              => 'required|string',
            'meta'              => 'required|string|max:255',
            'author_id'         => 'required|exists:article_authors,id',
            'video_url'         => 'required|url:http,https',
            'images'            => 'required|array|max:5',
            'images.*.image'    => 'required|image|max:2048'
        ];
    }
}
