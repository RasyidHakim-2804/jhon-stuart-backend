<?php
namespace App\Service\Contracts\Model;

interface CanUploadImage
{
    public function getDirPath():string;
    public function getDisk():string;
    public function getFormat():string;
    public function getOldImageUrl(): ?string;
}