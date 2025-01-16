<?php

namespace App\Notifications\Subscriber;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubscriberNotification extends Notification implements ShouldQueue
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
                ->subject('Welcome to ' . env('APP_NAME') . '!')
                ->greeting('Hello ' . $this->name . ',')
                ->line('Thank you for subscribing to ' . env('APP_NAME') . '!')
                ->line('We are excited to have you on board and will keep you updated with the latest news and offers.')
                ->line('If you have any questions, feel free to reach out to us anytime.')
                ->action('Visit Our Website', url('/'))
                ->line('Thank you for joining our community!')
                ->line('Contact us at: ' . env('MAIL_FROM_ADDRESS'));
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
