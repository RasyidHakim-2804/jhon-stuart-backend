<?php 
namespace App\Service\Services\ImageServices;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

class ImageServiceCollection
{
    /**
     * @var App\Service\Services\ImageServices\ImageService[]
     */
    private array $imageServices;
    
    public function __construct(Collection|array $collection)
    {
        $this->imageServices = $this->makeImageServices($collection);
    }

    public function upload(UploadedFile $image)
    {
        $this->imageServices = array_map(function($imageService) use ($image){
            
            $imageService->upload($image);
            
            return $imageService;
        
        }, $this->imageServices);
    }

    public function update(UploadedFile $image)
    {
        $this->imageServices = array_map(function($imageService) use ($image){
            
            $imageService->update($image);

            return $imageService;

        }, $this->imageServices);
    }

    public function delete()
    {
        $this->imageServices = array_map(function($imageService){
            
            $imageService->delete();

            return $imageService;

        }, $this->imageServices);
    }

    public function rollback()
    {
        $this->imageServices = array_map(function($imageService){
            
            $imageService->rollback();

            return $imageService;
            
        }, $this->imageServices);
    }

    private function makeImageServices($collection)
    {
        $imageServices = [];

        foreach ($collection as $model) {
            $imageServices[] = new ImageService($model);
        }

        return $imageServices;
    }
}