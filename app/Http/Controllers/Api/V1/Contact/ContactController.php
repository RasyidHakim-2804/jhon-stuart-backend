<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Contact\CreateContactRequest;
use App\Http\Resources\Common\SuccessResource;
use App\Mail\ContactMail;
use App\Models\Contact;
use App\Traits\JsonResponsesTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use JsonResponsesTrait;

    public function store(CreateContactRequest $request)
    {
        try {
            DB::beginTransaction();

            $contact = new Contact();
            $contact->name          = $request->name;
            $contact->email         = $request->email;
            $contact->phone         = $request->phone;
            $contact->message       = $request->message;
            $contact->save();

            DB::commit();

            $data                       = [];
            $data['contact_page_url']   = env('FRONTEND_URL') . '/admin/contact';

            Mail::to(config('mail.from.address'))->send(new ContactMail($data));

            return $this->successStore();

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }

    }
}
