<?php

namespace App\Models;

use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
            get: function($value = null) {

                if ($value == null) {
                    
                    return null;
                    
                } elseif ((Str::contains($value, 'https://')) || Str::contains($value,'http://')) {
                    
                    return $value;
                }
                
                return config('app.frontend_url') . 'storage/' . $value;
            }
        );
    }
}
