<?php

namespace App\Models\Image;

use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model implements CanUploadImage
{
    use HasFactory, HasImage;

    protected $table            = 'event_images';
    protected $imageUrlColumn   = 'url';
    protected $format           = '.webp';
    protected $disk             = 'public';
    protected $dirPath          = 'upload/images/event/';

    //Accessor path
    public function url(): Attribute
    {
        return new Attribute(
            get: function ($value = null) {
                if ($value == null) {
                    return null;
                }
                return  config('app.frontend_url') . 'storage/' . $value;
            }
        );
    }

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }
}
