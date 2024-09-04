<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductCategory\ProductCategoryResource;

class ProductResource extends JsonResource
{
    private bool $admin;

    public function __construct($resource,bool $admin = false)
    {
        $this->admin = $admin;
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'is_varied'         => $this->is_varied,
            'available'         => $this->available,
            'full_description'  => $this->full_description,
            'short_description' => $this->short_description,
            'display_price'     => $this->price,
            'category'          => new ProductCategoryResource($this->whenLoaded('category')),
            'images'            => ProductImageResource::collection($this->whenLoaded('images')),
            
            $this->mergeWhen(!$this->admin, function(){
                return [
                    'thumbnail_image'   => new ProductImageResource($this->whenLoaded('thumbnailImage')),
                    'variants'          => ProductVariantResource::collection($this->whenLoaded('variants')),
                ];
            }),

            $this->mergeWhen( $this->relationLoaded('variants') && $this->admin, function(){
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

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
