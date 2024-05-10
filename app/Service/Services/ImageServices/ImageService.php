<?php
namespace App\Service\Services\ImageServices;

use App\Service\Contracts\Model\CanUploadImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
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
    private $pathColumn;
    private $format;
    private $path;
    private $name;

    public function __construct(CanUploadImage $model)
    {
        $this->dirPath    = $model->getDirPath();
        $this->disk       = $model->getDisk();
        $this->pathColumn = $model->getPathColumn();
        $this->format     = $model->getFormat();
        $this->name       = $this->setName();
        $this->path       = $this->setPath();
    }

    //////////////////////////
    // public CRUD function //
    /////////////////////////
    
    public function upload(UploadedFile $image, ?string $name = null, ?string $suffixPath = null)
    {
        if ($name != null) $this->name = $name;
        if ($suffixPath != null) $this->path = $this->path . '/' . $suffixPath;

        $this->validateType($image);
        $this->store($image);

        return (object) [
            'path'  => $this->getPath(),
            'name'  => $this->getName(),
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
        if ( $this->pathColumn == false) {
            return null;
        }

        if (Storage::disk($this->disk)->exists($this->pathColumn)) {
            return Storage::disk($this->disk)->delete($this->pathColumn);
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

    protected function setPath(): string
    {
        return $this->dirPath . $this->getName();
    }

    protected function getPath()
    {
        return $this->path;
    }

    protected function setName(): string
    {
        $name = Hash::make(Str::uuid());

        return str_replace(['.', '/', '\\'],'', $name)  . $this->format;
    }

    protected function getName()
    {
        return $this->name;
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

    private function store($image)
    {
        $image = $this->imagePreProcessing($image);

        Storage::disk($this->disk)->put($this->getPath(), $image);
    }
}