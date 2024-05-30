<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Category\ProductCategory\ProductCategoryResource;

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
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'description'       => $this->translations->first()->description,
            'full_description'  => $this->translations->first()->full_description,
            'is_varied'         => $this->is_varied,
            'variants'          => ProductVariantResource::collection($this->whenLoaded('variants')),
            'categories'        => ProductCategoryResource::collection($this->whenLoaded('categories')),
            'images'            => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
