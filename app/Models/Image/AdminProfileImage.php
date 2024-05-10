<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Service\Abstracts\Model\MyBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Admin;

class AdminProfileImage extends MyBaseModel
{
    use HasFactory;

    protected $table      = 'admin_profile_images';
    protected $pathColumn = 'path';
    protected $format     = '.webp';
    protected $disk       = 'public';
    protected $dirPath    = 'upload/images/profile/admin/';

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function path(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => config('app.frontend_url') . 'storage/' . $value
        );
    }
}
