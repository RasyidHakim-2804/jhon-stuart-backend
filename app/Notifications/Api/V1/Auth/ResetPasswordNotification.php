<?php

namespace App\Notifications\Api\V1\Auth;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private $user,
        private $otp,
        private $expired_at,
        private string $prefix = 'user'
    )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Otp Reset Password')
                    ->greeting('Hello,' . $this->user->first_name)
                    ->line('otp: '. $this->otp);
                    // ->view('OtpForgetPassword', [
                    //     'name'       => $this->user->first_name,
                    //     'otp'        => $this->otp,
                    //     'resend'     => config('app.url') . '/api/v1/'. $this->prefix . '/password/reset/resend?email=' . $this->user->email,
                    //     'expired_at' => $this->expired_at,
                    // ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
