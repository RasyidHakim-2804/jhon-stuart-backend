<?php

namespace App\Notifications\Subscriber;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteSubscriberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Subscription Canceled')
                ->greeting('Hello ' . $this->name . ',')
                ->line('We wanted to inform you that your subscription to ' . env('APP_NAME') . ' has been canceled by our team.')
                ->line('If you believe this was done in error or if you wish to resubscribe, please don\'t hesitate to contact us.')
                ->action('Contact Support', 'mailto:' . env('MAIL_FROM_ADDRESS'))
                ->line('Thank you for being part of our community.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
