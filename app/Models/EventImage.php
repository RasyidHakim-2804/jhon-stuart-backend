<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $fillable = [
        'article_id',
    ];

    protected $imageSetting = [
        'folder' => 'event',
        'field'  => [
            'image',
        ],
    ];

    // === RELATIONS ===

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    // === SCOPES ===

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'event image', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'event image', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'event image', $data->id);
        });
    }
}
