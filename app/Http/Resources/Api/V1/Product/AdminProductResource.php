<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\ProductCategory\ProductCategoryResource;

class AdminProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'slug'               => $this->slug,
            'is_varied'          => $this->is_varied,
            'available'          => $this->available,
            'short_description'  => $this->short_description,
            'full_description'   => $this->full_description,

            $this->mergeWhen( $this->relationLoaded('variants'), function(){
                return [
                    'idr_price'     => $this->when(!$this->is_varied, $this->variants->first()->idr_price, null),
                    'usd_price'     => $this->when(!$this->is_varied, $this->variants->first()->usd_price, null),
                    'stock'         => $this->when(!$this->is_varied, $this->variants->first()->stock, null),
                    'variants'      => $this->when(
                        $this->is_varied,
                        ProductVariantResource::collection($this->variants), 
                        null),
                    ];
            }),
               
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
