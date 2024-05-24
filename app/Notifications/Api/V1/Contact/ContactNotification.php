<?php

namespace App\Notifications\Api\V1\Contact;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactNotification extends Notification
{
    use Queueable;

    private $email;
    private $name;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->email    = $contact->email;
        $this->name     = $contact->first_name . ' ' . $contact->last_name;
        $this->message  = $contact->message;
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
                    ->from($this->email, $this->name)
                    ->line('Hai admin, you get message from ' . $this->name)
                    ->line($this->message);
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
