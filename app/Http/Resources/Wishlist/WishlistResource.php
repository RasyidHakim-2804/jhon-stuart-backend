<?php

namespace App\Http\Resources\Wishlist;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductVariantResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'product_active'    => $this->product_active,
            'variant_active'    => $this->variant_active,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'deleted_at'        => $this->deleted_at,
            'product'           => new ProductResource($this->product),
            'variant'           => new ProductVariantResource($this->variant),
        ];
    }
}
