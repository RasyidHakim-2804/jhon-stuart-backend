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
use Illuminate\Support\Facades\DB;

class UserProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = auth()->user()->with('imageProfile')->firstOrFail();

        return new UserProfileResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request)
    {
        DB::transaction(function() use ($request)
        {
            /** @var User */
            $user = auth()->user();
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->save();

            if ($request->has('image')) {
                $this->setImage($request->image, $user->id);
            }
        });

        return new SuccessResource();
    }

    //////////////////////////
    //   private methods    //
    //////////////////////////

    private function setImage(UploadedFile $uploadedImage, $user_id)
    {
        /** @var UserProfileImage */
        $image          = UserProfileImage::query()->where('user_id', $user_id)->firstOrNew();
        $imageService   = new ImageService($image);


        if ($image->id) {
            $result = $imageService->update($uploadedImage);
        } else {
            $result = $imageService->upload($uploadedImage);
        }
        
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->save();
    }
}
