<?php
namespace App\Service\Services\ImageServices;

use App\Service\Contracts\Model\CanUploadImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

/**
 * Service class for handling image upload, update, and rollback operations.
 */
class ImageService
{
    /**
     * @var string The directory path where images are stored.
     */
    private $modelDirPath;

    /**
     * @var string The disk used for storing images.
     */
    private $modelDisk;

    /**
     * @var string|null The old image URL before update.
     */
    private $modelOldImageUrl;

    /**
     * @var string The format of the image file.
     */
    private $modelFormat;

    /**
     * @var string|null The URL of the newly uploaded image.
     */
    private $newImageUrl;

    /**
     * @var string|null The URL of the deleted image before rollback.
     */
    private $deletedImageUrl;

    /**
     * @var string The directory path where deleted images are moved.
     */
    private $deletedDirectory = 'imageService/deleted/';

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
    
    /**
     * Uploads an image and returns information about the uploaded file.
     *
     * @param UploadedFile $image The image file to upload.
     * @return object An object containing information about the uploaded file:
     *                - path: The path where the file is stored.
     *                - name: The generated name of the file.
     */
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

    /**
     * Updates the content with a new file upload.
     *
     * This method deletes any existing content and uploads a new file, returning information about the uploaded file.
     *
     * @param UploadedFile $content The new content file to upload.
     * @return object An object containing information about the newly uploaded file:
     *                - path: The path where the file is stored.
     *                - name: The generated name of the file.
     */
    public function update(UploadedFile $content)
    {
        $this->delete();
        return $this->upload($content);
    }

    /**
     * Deletes the old image file if it exists.
     *
     * Checks if the old image URL exists in the specified disk.
     *
     * @return bool|null True if the file was moved to the deleted directory, null otherwise.
     */
    public function delete(): bool|null
    {
        if ( ($this->modelOldImageUrl != null) &&
             ( Storage::disk($this->modelDisk)->exists($this->modelOldImageUrl) ) ) {

            $this->deletedImageUrl = $this->deletedDirectory . $this->modelOldImageUrl;
        
            return Storage::disk($this->modelDisk)->move(
                from: $this->modelOldImageUrl,
                to: $this->deletedImageUrl
            );
        }
        return null;
    }

    /**
     * Rolls back changes by deleting new image and restoring deleted image.
     */
    public function rollback()
    {
        if( isset($this->newImageUrl) ) {
            Storage::disk($this->modelDisk)->delete($this->newImageUrl);

            unset($this->newImageUrl);
        }

        if ( isset($this->deletedImageUrl) ) {

            Storage::disk($this->modelDisk)->move(
                from: $this->deletedImageUrl,
                to: $this->modelOldImageUrl
            );

            unset($this->deletedImageUrl);
        }
    }

    public function __destruct()
    {
        if ( isset($this->deletedImageUrl) ) {
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