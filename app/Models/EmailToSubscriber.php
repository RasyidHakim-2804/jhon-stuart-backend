<?php

namespace App\Models;

use App\Services\Logs\AdminLogService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailToSubscriber extends Model
{
    use HasFactory;

    // === RELATIONS ===

    public function mailer(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'mailer_admin_id', 'id');
    }

    // === BOOT ===

    public static function boot()
    {
        parent::boot();

        $logService = new AdminLogService;

        static::created(function($data) use($logService) {
            $logService->store('create', 'product', $data->id);
        });

        static::updated(function($data) use($logService) {
            if ($logService) $logService->store('update', 'product', $data->id);
        });

        static::deleted(function($data) use($logService) {
            if ($logService) $logService->store('delete', 'product', $data->id);
        });
    }
}
