<?php 
namespace App\Service\Services\VerifyEmail;

use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Notifications\Api\V1\Auth\VerifyEmailNotification;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\AdminVerifyEmail;
use Illuminate\Http\Client\HttpClientException;

class AdminVerifyEmailService
{
    public static function send(Admin $admin)
    {
        $verifyEmail = AdminVerifyEmail::where('admin_id', $admin->id)->first();
        $token       = null;

        if ($verifyEmail !== null) {
            $token = $verifyEmail->token;
        } else {
            $token = self::createVerifyToken($admin);
        }

        $admin->notify(new VerifyEmailNotification($admin, $token, 'admins'));
    }
    

    public static function verifyEmail($token)
    {
        $verify = AdminVerifyEmail::where('token', $token)->first();

        $admin                      = Admin::where('email', $verify->email)->firstOrFail();
        $admin->active              = 1;
        $admin->email_verified_at   = now();
        $admin->save();
        
        $verify->delete();
    }

    ///////////////////
    //private methods//
    ///////////////////


    private static function createVerifyToken(Admin $admin)
    {
        $verifyEmail = new AdminVerifyEmail();
        $verifyEmail->admin_id  = $admin->id;
        $verifyEmail->email     = $admin->email;
        $verifyEmail->token     = Hash::make(now());
        $verifyEmail->save();

        return $verifyEmail->token;
    }

    
}