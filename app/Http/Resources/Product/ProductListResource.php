<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductCategory\ProductCategoryResource;

class ProductListResource extends JsonResource
{
    private bool $admin;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'type'              => $this->type,
            'popular'           => $this->popular,
            'rating'            => $this->rating,
            'is_varied'         => $this->is_varied,
            'is_active'         => $this->is_active,
            'in_wishlist'       => $this->in_wishlist? true : false,
            'meta'              => $this->meta,
            'thumbnail_price'   => $this->price,
            'images'            => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
