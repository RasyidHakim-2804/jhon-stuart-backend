<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Image\ProductImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'products';

    protected $fillable = [
        'name','slug'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('name') 
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function scopeSelectVariantPrice(Builder $query)
    {
        $query->addSelect('*',DB::raw(
            "(select usd_price from product_variants where product_id = products.id order by usd_price asc limit 1) as price"
        ));
    }

    public function scopeAvailable(Builder $query, bool $available = true)
    {
        $query->where('available', '=', $available);
    }

    public function thumbnailImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->ofMany('position', 'MIN');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function minPriceVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->ofMany('idr_price', 'MIN');
    }
}
