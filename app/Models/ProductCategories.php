<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProductCategories extends Model
{
    use HasFactory, HasSlug;

    protected $table                = 'product_categories';

    public static $categoryTypes    = [ 'museum', 'gallery' ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom(['type','name']) 
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'pivot_product_category',  'product_categories_id', 'product_id');
    }
}
