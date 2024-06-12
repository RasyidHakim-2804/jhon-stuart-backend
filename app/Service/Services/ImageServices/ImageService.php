<?php
namespace App\Service\Services\ImageServices;

use App\Service\Contracts\Model\CanUploadImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Baese image service for model can upload image
 */
class ImageService
{
    private $dirPath;
    private $disk;
    private $modelOldImageUrl;
    private $format;

    public function __construct(CanUploadImage $model)
    {
        $this->dirPath          = $model->getDirPath();
        $this->disk             = $model->getDisk();
        $this->format           = $model->getFormat();
        $this->modelOldImageUrl = $model->getOldImageUrl();
    }

    //////////////////////////
    // public CRUD function //
    /////////////////////////
    
    public function upload(UploadedFile $image, ?string $suffixPath = null)
    {
        $this->validateType($image);

        $name = $this->setName();
        $path = $this->setPath($name, $suffixPath);

        $this->store($image, $path);

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
        if ( $this->modelOldImageUrl == null) {
            return null;
        }

        if (Storage::disk($this->disk)->exists($this->modelOldImageUrl)) {
            return Storage::disk($this->disk)->delete($this->modelOldImageUrl);
        }
        return null;
    }

    /////////////
    //protected//
    /////////////

    protected function imagePreProcessing($image)
    {
        return Image::read($image)->scaleDown(1280, 720)->toWebp(80);
    }

    protected function setPath($name, ?string $suffixPath = null): string
    {
        if ($suffixPath) {
            return $this->dirPath . '/' . $suffixPath . '/' . $name;
        }
        return $this->dirPath . $name;
    }

    protected function setName(): string
    {
        $name = Str::ulid()->toBase32();

        return str_replace(['.', '/', '\\'],'', $name)  . $this->format;
    }

    /////////////
    // private //
    /////////////

    private function validateType(UploadedFile $image)
    {
        $validator = Validator::make(
            data: ['image' => $image],
            rules: ['image' => File::image()]
        );

        $validator->validate();
    }

    private function store($image, $path)
    {
        $image = $this->imagePreProcessing($image);

        Storage::disk($this->disk)->put($path, $image);
    }
}