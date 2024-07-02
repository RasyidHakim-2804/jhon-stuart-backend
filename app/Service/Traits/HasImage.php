<?php
namespace App\Service\Traits;

trait HasImage
{
    
    public function getDirPath():string
    {
        return trim($this->dirPath, '/') . '/';
    }

    public function getDisk():string
    {
        return $this->disk;
    }

    public function getFormat():string
    {
        return '.' . trim($this->format, '.');
    }

    /**
     * return path of image folder
     * this function for your model if already has image 
     */
    public function getOldImageUrl(): ?string
    {
        if ( isset( $this->attributes[$this->imageUrlColumn])){
            return $this->attributes[$this->imageUrlColumn];
        }  
        return null;
    }

    /**
     * The model must have the following attributes:
     */

    /**
     * column name containing the image path
     * 
     * protected $imageUrlColumn;
     */

    /**
     * column name containing the dir path to save the image
     * 
     * protected $dirPath              = 'images/';
     */

    /**
     * type of disk
     *
     * protected $disk                 = 'local';
     */

    /**
     * suffix for image, ex: .jpg, .png, .gif, etc.
     * 
     * protected $format               = '';
     */

}