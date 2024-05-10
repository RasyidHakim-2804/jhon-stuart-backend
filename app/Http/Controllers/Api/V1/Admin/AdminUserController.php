<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\V1\Admin\AdminUpdatesUserRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Profile\UserProfileResource;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::with('imageProfile')->paginate(25);

        return UserProfileResource::collection($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserProfileResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(User $user, AdminUpdatesUserRequest $request)
    {
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->save();

        return new SuccessResource();
    }
}
