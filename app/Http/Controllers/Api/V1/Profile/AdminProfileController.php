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

            if ($request->has('image')) {
                $this->setImage($request->image, $admin->id);
            }
        });

        return new SuccessResource();
    }

    //////////////////////////
    //   private methods    //
    //////////////////////////

    private function setImage(UploadedFile $uploadedImage, $admin_id)
    {
        /** @var AdminProfileImage */
        $image          = AdminProfileImage::query()->where('admin_id', $admin_id)->firstOrNew();
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
