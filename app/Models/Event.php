<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Event extends Model
{
    use HasFactory, ImageStorageTrait, HasSlug;

    protected $imageSetting = [
        'folder' => 'event',
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

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class, 'event_id', 'id');
    }

    // === TRANSLATION RELATIONS ===

    public function titleTranslations()
    {
        return $this->hasMany(EventTranslation::class, 'event_id', 'id')->where('field', 'title');
    }

    public function bodyTranslations()
    {
        return $this->hasMany(EventTranslation::class, 'event_id', 'id')->where('field', 'body');
    }

    public function metaTranslations()
    {
        return $this->hasMany(EventTranslation::class, 'event_id', 'id')->where('field', 'meta');
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
            $logService->store('create', 'event', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'event', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'event', $data->id);
        });
    }
}
