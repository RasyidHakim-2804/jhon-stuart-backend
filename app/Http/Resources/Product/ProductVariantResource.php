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
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'code'          => $this->code,
            'product_id'    => $this->product_id,
            $this->mergeWhen($this->relationLoaded('product'), function() {
                return [
                    'product_name'        => $this->product->name,
                    'product_available'   => $this->product->available,
                ];
            }),
            'type'          => $this->type,
            'available'     => $this->available,
            'idr_price'     => $this->idr_price,
            'usd_price'     => $this->usd_price,
            'stock'         => $this->stock,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
