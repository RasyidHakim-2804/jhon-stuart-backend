<?php

namespace App\Models;

use App\Models\Translation\ArticleTranslation;
use App\Models\Image\ArticleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function translation() : HasMany 
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    public function image() : HasOne
    {
        return $this->hasOne(ArticleImage::class);
    }
}
