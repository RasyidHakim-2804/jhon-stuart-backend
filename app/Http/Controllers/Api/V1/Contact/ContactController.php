<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Contact\CreateContactRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Mail\Api\V1\ContactMail;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
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

            Mail::to(config('mail.from.address'))->send(new ContactMail());

            return new SuccessResource(code: 201);

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }

    }
}
