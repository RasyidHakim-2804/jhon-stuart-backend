<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'language',
        'filed',
        'translation',
    ];

    public const ATTRIBUTE  = ['title', 'meta', 'body'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'event translation', $data->id);
        });

        static::updated(function($data) use($logService) {
            $logService->store('update', 'event translation', $data->id);
        });

        static::deleted(function($data) use($logService) {
            $logService->store('delete', 'event translation', $data->id);
        });
    }
}
