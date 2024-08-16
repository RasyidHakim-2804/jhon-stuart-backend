<?php

namespace App\Models;

use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory, HasSlug, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'article',
        'field'  => [
            'image',
        ],
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function author() : BelongsTo
    {
        return $this->belongsTo(ArticleAuthor::class);
    }
}
