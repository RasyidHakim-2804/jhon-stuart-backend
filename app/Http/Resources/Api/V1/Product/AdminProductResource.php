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
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'is_varied'     => $this->is_varied,
            'is_saleable'   => $this->is_saleable,

            $this->mergeWhen( $this->relationLoaded('categories'),[
                'category_type' => $this->categories->first()->type,
                'categories'    => ProductCategoryResource::collection($this->categories),    
            ]),

            $this->mergeWhen( $this->relationLoaded('variants'), [
                'size'          => $this->when(!$this->is_varied, $this->variants->first()->size, null),
                'idr_price'     => $this->when(!$this->is_varied, $this->variants->first()->idr_price, null),
                'usd_price'     => $this->when(!$this->is_varied, $this->variants->first()->usd_price, null),
                'stock'         => $this->when(!$this->is_varied, $this->variants->first()->stock, null),
                'variants'      => $this->when( $this->is_varied, function() {
                    return ProductVariantResource::collection($this->variants);
                }, null),
            ]),
               
            'translations'  => ProductTranslationResource::collection($this->whenLoaded('translations')),    
            
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
