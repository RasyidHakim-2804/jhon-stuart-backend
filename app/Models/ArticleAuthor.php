<?php

namespace App\Models;

use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleAuthor extends Model implements CanUploadImage
{
    use HasFactory, HasImage;

    protected $table            = 'article_authors';
    protected $imageUrlColumn   = 'image_url';
    protected $format           = '.webp';
    protected $disk             = 'public';
    protected $dirPath          = 'upload/images/author/';

    public function article() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => config('app.frontend_url') . 'storage/'. $value
        );
    }
}
