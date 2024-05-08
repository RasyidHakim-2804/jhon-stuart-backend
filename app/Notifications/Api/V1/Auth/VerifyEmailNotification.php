<?php

namespace App\Notifications\Api\V1\Auth;

use App\Models\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    private $user, $token, $prefix, $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user,$token,$prefix = 'user')
    {
        $this->user   = $user;
        $this->token  = $token;
        $this->prefix = $prefix;
        $this->url    = config('app.frontend_url') . $this->prefix .'/email-verify/confirm/' . $this->token;
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
                    ->subject('Email Verification')
                    ->line('hello' . $this->user->first_name)
                    ->action('Verify',$this->url);
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
