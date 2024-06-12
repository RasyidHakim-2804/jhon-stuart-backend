<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Admin;
use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;

class AdminProfileImage extends Model implements CanUploadImage
{
    use HasFactory, HasImage;

    protected $table          = 'admin_profile_images';
    protected $imageUrlColumn = 'path';
    protected $format         = '.webp';
    protected $disk           = 'public';
    protected $dirPath        = 'upload/images/profile/admin/';

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
