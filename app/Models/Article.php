<?php

namespace App\Models;

use App\Traits\ImageStorageTrait;
use App\Services\Logs\AdminLogService;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableInterface;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model implements TranslatableInterface
{
    use HasFactory, HasSlug, ImageStorageTrait, Translatable;

    protected $imageSetting = [
        'folder' => 'article',
        'field'  => [
            'image',
        ],
    ];

    public const TYPE = [
        'blog', 'event'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
                ->generateSlugsFrom('title')
                ->saveSlugsTo('slug')
                ->slugsShouldBeNoLongerThan(100)
                ->startSlugSuffixFrom(2);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // === relation ===

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArticleImage::class, 'article_id', 'id');
    }

    public function video(): HasOne
    {
        return $this->hasOne(ArticleVideo::class, 'article_id', 'id');
    }

    // public function translations(): HasMany -> in trait Translatalbe

    // === scope ===

    public function scopeEvent(Builder $query)
    {
        $query->where('type', '=', 'event');
    }

    public function scopeBlog(Builder $query)
    {
        $query->where('type', '=', 'blog');
    }

    // === boot ===

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'article', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'article', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'article', $data->id);
        });
    }
}
