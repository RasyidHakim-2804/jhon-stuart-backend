<?php

namespace App\Http\Requests\Api\Wishlist;

use App\Models\Wishlist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteWishlistRequest extends FormRequest
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
            'product_variant_id' => [
                'required',
                Rule::exists('wishlists', 'product_variant_id')
                ->where('user_id', $this->user()->id)
            ]
        ];
    }
}
