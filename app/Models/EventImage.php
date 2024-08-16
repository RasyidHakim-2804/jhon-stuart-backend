<?php

namespace App\Models\Image;

use App\Traits\ImageStorageTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    use HasFactory, ImageStorageTrait;

    protected $imageSetting = [
        'folder' => 'event',
        'field'  => [
            'image',
        ],
    ];

    public function scopeSaved(Builder $query)
    {
        $query->where('position', '!=', null);
    }
}
