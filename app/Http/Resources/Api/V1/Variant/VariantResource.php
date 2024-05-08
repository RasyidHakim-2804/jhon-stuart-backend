<?php

namespace App\Http\Resources\Api\V1\Variant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VariantResource extends JsonResource
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
            '_price'        => $this->price,
            'usd_price'     => $this->usd_price,
            'stock'         => $this->stock,
            'max_order'     => $this->max_order,
            'min_order'     => $this->min_order,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
