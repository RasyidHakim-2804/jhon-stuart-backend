<?php
namespace App\Service\Services\ImageServices;

use App\Service\Contracts\Model\CanUploadImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Baese image service for model can upload image
 */
class ImageService
{
    private $modelDirPath;
    private $modelDisk;
    private $modelOldImageUrl;
    private $modelFormat;

    private $newImageUrl        = null;
    private $deletedImageUrl    = null;
    private $deletedDirectory   = 'imageService/deleted/';


    public function __construct(CanUploadImage $model)
    {
        $this->modelDirPath          = $model->getDirPath();
        $this->modelDisk             = $model->getDisk();
        $this->modelFormat           = $model->getFormat();
        $this->modelOldImageUrl      = $model->getOldImageUrl();
    }

    //////////////////////////
    // public CRUD function //
    /////////////////////////
    
    public function upload(UploadedFile $image)
    {
        $this->validateType($image);

        $name = str_replace(['.', '/', '\\'],'', Str::ulid()->toBase32())  . $this->modelFormat;
        $path = $this->modelDirPath . $name;

        $this->store($image, $path);
        $this->newImageUrl = $path;

        return (object) [
            'path'  => $path,
            'name'  => $name,
        ];
    }

    public function update(UploadedFile $content, ?string $name = null)
    {
        $this->delete();
        return $this->upload($content, $name);
    }

    /**
     * will return null if the file does not exist
     */
    public function delete(): bool|null
    {
        if ( ($this->modelOldImageUrl != null) &&
             ( Storage::disk($this->modelDisk)->exists($this->modelOldImageUrl) ) ) {

            $this->deletedImageUrl = $this->deletedDirectory . $this->modelOldImageUrl;
        
            Storage::disk($this->modelDisk)->move(
                from: $this->modelOldImageUrl,
                to: $this->deletedImageUrl
            );
        }
        return null;
    }

    public function rollback()
    {
        if( $this->newImageUrl != null ) {
            Storage::disk($this->modelDisk)->delete($this->newImageUrl);

            $this->newImageUrl = null;
        }

        if ( $this->deletedImageUrl != null ) {

            Storage::disk($this->modelDisk)->move(
                from: $this->deletedImageUrl,
                to: $this->modelOldImageUrl
            );

            $this->deletedImageUrl = null;
        }
    }

    public function __destruct()
    {
        if ($this->deletedImageUrl != null) {
            Storage::disk($this->modelDisk)->delete($this->deletedImageUrl);
        }
    }

    /////////////
    //protected//
    /////////////

    protected function imagePreProcessing($image)
    {
        switch ($this->modelFormat) {
            case '.jpeg':
            case '.jpg':
                return Image::read($image)->scaleDown(1280, 720)->toJpeg(80);
                break;
            
            case '.png':
                return Image::read($image)->scaleDown(1280, 720)->toPng(80);
                break;
            
            default:
                return Image::read($image)->scaleDown(1280, 720)->toWebp(80);
                break;
        }
    }

    /////////////
    // private //
    /////////////

    private function validateType(UploadedFile $image)
    {
        $validator = Validator::make(
            data: ['image' => $image],
            rules: ['image' => 'image']
        );

        $validator->validate();
    }

    private function store($image, $path)
    {
        $image = $this->imagePreProcessing($image);

        Storage::disk($this->modelDisk)->put($path, $image);
    }
}