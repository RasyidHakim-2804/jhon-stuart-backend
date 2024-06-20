<?php

namespace App\Models;

use App\Models\Translation\ArticleTranslation;
use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Article extends Model implements CanUploadImage
{
    use HasFactory, HasSlug, HasImage;

    protected $imageUrlColumn   = 'image_url';
    protected $format           = '.webp';
    protected $disk             = 'public';
    protected $dirPath          = 'upload/images/article/';

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: function(string $value) {

                if (Str::contains($value, 'https://') || Str::contains($value,'http://')) {
                    return $value;
                }
                
                return config('app.frontend_url') . 'storage/' . $value;
            }
        );
    }

    public function translations() : HasMany 
    {
        return $this->hasMany(ArticleTranslation::class);
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
