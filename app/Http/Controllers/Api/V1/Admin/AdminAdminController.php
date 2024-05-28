<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\AdminCreatesAdminRequest;
use App\Http\Requests\Api\V1\Admin\AdminUpdatesAdminRequest;
use App\Http\Resources\Api\V1\Common\FailedResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Http\Resources\Api\V1\Profile\AdminProfileResource;
use App\Models\Admin;
use App\Service\Services\VerifyEmail\AdminVerifyEmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminAdminController extends Controller
{
       /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::with('imageProfile')->paginate(25);

        return AdminProfileResource::collection($admins);
    }

    public function store(AdminCreatesAdminRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $admin              = new Admin();
            $admin->uuid        = Str::ulid(now())->toBase58();
            $admin->first_name  = $request->first_name;
            $admin->last_name   = $request->last_name;
            $admin->email       = $request->email;
            $admin->active      = 0;
    
            $admin->save();
            
            DB::commit();
            
            AdminVerifyEmailService::send($admin);

            return new SuccessResource(201);    
    
        } catch (\Exception $e) {
            DB::rollBack();
            return new FailedResource(500, $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return new AdminProfileResource($admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUpdatesAdminRequest $request, Admin $admin)
    {
        $admin->first_name  = $request->first_name;
        $admin->last_name   = $request->last_name;
        $admin->active      = $request->active;

        $admin->save();

        return new SuccessResource();
    }
}
