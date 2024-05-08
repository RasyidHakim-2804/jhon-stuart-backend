<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminVerifyEmail extends Model
{
    use HasFactory;

    public function admin():BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
