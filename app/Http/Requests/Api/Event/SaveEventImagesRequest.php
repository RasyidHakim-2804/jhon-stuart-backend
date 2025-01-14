<?php

namespace App\Http\Requests\Api\Event;

use App\Models\EventImage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveEventImagesRequest extends FormRequest
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
        return [
            'images_id'      => 'array|max:4',
            'images_id.*'    => [
                'required',
                'numeric',
                'distinct',
                Rule::exists(EventImage::class, 'id')->where(function($query){
                    $query->where('event_id', $this->route('event')->id);
                })
            ],
        ];
    }
}
