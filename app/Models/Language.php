<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    // public const LANGUAGE = ['en', 'id'];

    public static function code()
    {
        return self::pluck('code')->toArray();
    }
}
