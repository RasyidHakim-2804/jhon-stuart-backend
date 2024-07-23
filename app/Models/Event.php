<?php

namespace App\Models;

use App\Models\Image\EventImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Event extends Model
{
    use HasFactory, HasSlug;

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('title') 
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(ArticleAuthor::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class);
    }
}
