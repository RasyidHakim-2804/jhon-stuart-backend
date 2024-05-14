<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\Admin\AdminUpdateRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Profile\AdminProfileResource;
use App\Models\Admin;
use App\Models\Image\AdminProfileImage;
use App\Service\Services\ImageServices\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AdminProfileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show()
    {
        $email = auth()->user()->email;

        $admin = Admin::with('imageProfile')->where('email', $email)->firstOrFail();

        return new AdminProfileResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdateRequest $request)
    {
        DB::transaction(function() use ($request)
        {
            /** @var Admin */
            $admin              = Admin::where('email', $request->email)->firstOrFail();

            $admin->first_name  = $request->first_name;
            $admin->last_name   = $request->last_name;

            $admin->save();

            if ($admin->imageProfile() === null) {

                $this->createImage($request->image, $admin->id);
            } else {

                $this->updateImage($request->image, $admin->id);
            }
        });

        return new SuccessResource();
    }

    //////////////////////////
    //   private methods    //
    //////////////////////////

    private function createImage(UploadedFile $uploadedImage, $admin_id)
    {
        $image          = new AdminProfileImage();
        $imageService   = new ImageService($image);

        $result             = $imageService->upload($uploadedImage);
        $image->admin_id    = $admin_id;
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->save();
    }

    private function updateImage(UploadedFile $uploadedImage, $admin_id)
    {
        $image          = AdminProfileImage::where('admin_id', $admin_id)->first();
        $imageService   = new ImageService($image);

        $result             = $imageService->update($uploadedImage);
        $image->name        = $result->name;
        $image->path        = $result->path;
        $image->save();
    }
}
