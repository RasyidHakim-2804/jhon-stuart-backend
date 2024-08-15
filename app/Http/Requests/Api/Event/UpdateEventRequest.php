<?php

namespace App\Http\Requests\Api\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'images'            => 'nullable|array|max:5',
            'images.*.image'    => 'exclude_if:images,null|image|max:2048|required',
            'images.*.position' => 'exclude_if:images,null|numeric|min:1|max:5|distinct|required'
        ];
    }
}
