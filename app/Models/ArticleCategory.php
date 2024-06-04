<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ArticleCategory extends Model
{
    use HasFactory, HasSlug;

    protected $table = 'article_categories';

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->saveSlugsTo('slug')
                ->generateSlugsFrom('name')
                ->startSlugSuffixFrom(2);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
