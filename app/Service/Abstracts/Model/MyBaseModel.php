<?php
namespace App\Service\Abstracts\Model;

use App\Service\Contracts\Model\CanUploadImage;
use App\Service\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

abstract class MyBaseModel extends Model implements CanUploadImage
{
    use HasImage;

    /**
     * column name containing the image path
     */
    protected $pathColumn;

    /**
     * column name containing the dir path to save the image
     */
    protected $dirPath              = 'images/';

    /**
     * type of disk
     */
    protected $disk                 = 'local';

    /**
     * suffix for image, ex: .jpg, .png, .gif, etc.
     */
    protected $format               = '';
}