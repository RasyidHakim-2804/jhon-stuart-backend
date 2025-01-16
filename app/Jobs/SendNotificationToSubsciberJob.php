<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Notifications\Subsriber\SubscriberNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendNotificationToSubsciberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subscriber;
    private $subject;
    private $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, string $subject, string $message)
    {
        $this->subscriber   = $subscriber;
        $this->subject      = $subject;
        $this->message      = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->subscriber->notify(new SubscriberNotification(
            $this->subject,
            $this->message,
            $this->subscriber->first_name
        ));
    }
}
