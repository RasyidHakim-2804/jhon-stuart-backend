<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\User\UserUpdateRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Profile\UserProfileResource;
use App\Models\Image\UserProfileImage;
use App\Models\User;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\UploadedFile;

class UserProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $email = auth()->user()->email;

        $user = User::with('imageProfile')->where('email', $email)->firstOrFail();

        return new UserProfileResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->save();

        if ($user->imageProfile() === null) {

            $this->createImage($request->image, $user->id);
        } else {

            $this->updateImage($request->image, $user->id);
        }

        return new SuccessResource();
    }

    //////////////////////////
    //   private methods    //
    //////////////////////////

    private function createImage(UploadedFile $uploadedImage, $admin_id)
    {
        $image          = new UserProfileImage();
        $imageService   = new ImageService($image);

        $result             = $imageService->upload($uploadedImage);
        $image->admin_id    = $admin_id;
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->save();
    }

    private function updateImage(UploadedFile $uploadedImage, $admin_id)
    {
        $image          = UserProfileImage::where('admin_id', $admin_id)->first();
        $imageService   = new ImageService($image);

        $result             = $imageService->update($uploadedImage);
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->save();
    }
}
