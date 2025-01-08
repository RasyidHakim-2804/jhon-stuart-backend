<?php

namespace App\Models;

use App\Observers\AdminActionObserver;
use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Builder;

class ProductCategory extends Model
{
    use HasFactory, HasSlug;

    public const MUSEUM_TYPE  = 'museum';

    public const GALLERY_TYPE = 'gallery';

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    // === RELATIONS ===

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // === SCOPE ===

    public function scopeGallery(Builder $query)
    {
        $query->where('type', '=', self::GALLERY_TYPE);
    }

    public function scopeMuseum(Builder $query)
    {
        $query->where('type', '=', self::MUSEUM_TYPE);
    }

    // === BOOT ===

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'product_category', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'product_category', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'product_category', $data->id);
        });
    }
}
