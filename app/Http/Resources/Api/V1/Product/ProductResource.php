<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Variant\VariantResource;
use App\Http\Resources\Api\V1\Category\CategoryResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'description'   => $this->description,
            'is_varied'     => $this->is_varied,
            'variants'      => VariantResource::collection($this->whenLoaded('variants')),
            'categories'    => CategoryResource::collection($this->whenLoaded('categories')),
            'images'        => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
