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
            'product_id'    => $this->product_id,
            $this->mergeWhen($this->relationLoaded('product'), function() {
                return [
                    'product_name'  => $this->product->name,
                    'is_saleable'   => $this->product->is_saleable,
                ];
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
