<?php

namespace App\Jobs;

use App\Notifications\Subsriber\MessageFromAdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendEmailToSubsciberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $name;
    private $subject;
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $name, string $subject, string $message)
    {
        $this->email   = $email;
        $this->name    = $name;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Notification::route('mail', $this->email)->notify(new MessageFromAdminNotification(
            $this->subject,
            $this->message,
            $this->name
        ));
    }
}
