<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasFactory, ImageStorageTrait, HasSlug;

    public const TYPE = ['blog', 'community'];

    protected $fillable = [
        'type'
    ];

    protected $imageSetting = [
        'folder' => 'articles',
        'field'  => [
            'image',
        ],
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // === RELATIONS ===

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArticleImage::class, 'article_id', 'id');
    }

    // === TRANSLATION RELATIONS ===

    public function titleTranslations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id')->where('field', 'title');
    }

    public function bodyTranslations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id')->where('field', 'body');
    }

    public function metaTranslations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id')->where('field', 'meta');
    }

    // === SCOPE ===

    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'LIKE','%' . $search . '%');
    }

    // === BOOT ===

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
