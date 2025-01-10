<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // === RELATIONS ===

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

    // === SCOPE ===

    public function scopeSelectVariantPrice(Builder $query)
    {
        $query->addSelect('*',DB::raw(
            "(select usd_price from product_variants " .
            "where product_id = products.id " .
            "order by usd_price asc limit 1) as price"
        ));
    }

    public function scopeActive(Builder $query, bool $active = true)
    {
        $query->where('is_active', '=', $active);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE','%' . $search . '%');
    }

    public function scopeInWishlist(Builder $query, $user_id = 0)
    {
        $query->addSelect('*',DB::raw(
            "(select user_id from wishlists " .
            "where product_id = products.id " .
            "and user_id = " . intval($user_id) . " " .
            "limit 1) as in_wishlist"
        ));
    }

    // === BOOT ===

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'product', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'product', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'product', $data->id);
        });
    }
}
