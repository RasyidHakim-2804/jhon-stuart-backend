<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Service\Abstracts\Model\MyBaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserProfileImage extends MyBaseModel
{
    use HasFactory;

    protected $table      = 'user_profile_images';
    protected $pathColumn = 'path';
    protected $format     = '.webp';
    protected $disk       = 'public';
    protected $dirPath    = 'upload/images/profile/user/';

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function path(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => config('app.frontend_url') . 'storage/' . $value
        );
    }
}
