<?php

namespace App\Jobs;

use App\Mail\Subscriber\EmailFromAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailToSubsciberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $subject;
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $subject, string $message)
    {
        $this->email   = $email;
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
        Mail::to($this->email)->send(new EmailFromAdmin(
            $this->subject,
            $this->message
        ));
    }
}
