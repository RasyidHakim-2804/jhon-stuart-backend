<?php
namespace App\Service\Traits;

trait HasImage
{
    
    public function getDirPath():string
    {
        return $this->dirPath;
    }

    public function getDisk():string
    {
        return $this->disk;
    }

    public function getFormat():string
    {
        return $this->format;
    }

    /**
     * return path of image folder
     * this function for your model if already has image 
     */
    public function getPathColumn()
    {
        if ( isset( $this->attributes[$this->pathColumn])){
            return $this->attributes[$this->pathColumn];
        }  
        return false;
    }
}