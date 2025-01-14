<?php

namespace App\Http\Requests\Api\Event;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
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
            'video'         => 'required_if:type,event|url',
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
