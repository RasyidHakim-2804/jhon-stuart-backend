<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'size'          => $this->size,
            'product_id'    => $this->product_id,
            'product_name'  => $this->whenLoaded('product', function() {
                return $this->product->name;
            }),
            'is_default'    => $this->is_default,
            'idr_price'     => $this->idr_price,
            'usd_price'     => $this->usd_price,
            'stock'         => $this->stock,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
