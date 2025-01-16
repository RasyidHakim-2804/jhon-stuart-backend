<?php

namespace App\Http\Controllers\Api\V1\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscriber\SendEmailToSubscriberRequest;
use App\Models\Subscriber;
use App\Http\Resources\Subscriber\SubscriberResource;
use App\Jobs\SendEmailToSubsciberJob;
use App\Models\EmailToSubscriber;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;

class AdminSubscriberController extends Controller
{
    use JsonResponsesTrait;

    public function index()
    {
        $subscribers = Subscriber::paginate(25);

        return SubscriberResource::collection($subscribers);
    }

    public function sendEmailToAllSubscribers(SendEmailToSubscriberRequest $request)
    {
        DB::beginTransaction();
        try {

            $message                  = new EmailToSubscriber();
            $message->subject         = $request->subject;
            $message->message         = $request->message;
            $message->mailer_admin_id = auth()->user()->id;
            $message->save();

            DB::commit();

            Subscriber::pluck('first_name', 'email')->chunk(100)->each(function($subscribers) use ($message) {
                foreach ($subscribers as $email => $name) {
                    dispatch(new SendEmailToSubsciberJob(
                        $email,
                        $name,
                        $message->subject,
                        $message->message
                    ));
                }
            });

            return $this->successResponse();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(Subscriber $subscriber)
    {
        DB::beginTransaction();
        try {

            $subscriber->delete();

            DB::commit();

            return $this->successResponse();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
