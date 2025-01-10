<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'product_id'    => $this->product_id,
            'name'          => $this->name,
            'is_active'     => $this->is_active,
            'is_default'    => $this->is_default,
            'price'         => $this->price,
            'usd_price'     => $this->usd_price,
            'stock'         => $this->stock,
            'product'       => new ProductResource($this->whenLoaded('product')),
            'in_wishlist'   => $this->in_wishlist? true : false,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
