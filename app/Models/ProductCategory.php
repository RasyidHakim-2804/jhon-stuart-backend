<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Translation\ProductCategoryTranslation;
use Illuminate\Database\Eloquent\Builder;

class ProductCategory extends Model
{
    use HasFactory, HasSlug;

    protected $table          = 'product_categories';

    public const MUSEUM_TYPE  = 'museum';
    public const GALLERY_TYPE = 'gallery';

    public function scopeGallery(Builder $query)
    {
        $query->where('type', '=', self::GALLERY_TYPE);
    }

    public function scopeMuseum(Builder $query)
    {
        $query->where('type', '=', self::MUSEUM_TYPE);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom(['type','name']) 
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
