<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Requests\Api\V1\Contact\CreateRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Common\FailedResource;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Mail\ContactMail;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(CreateRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $contact = new Contact();
            $contact->first_name    = $request->first_name;
            $contact->last_name     = $request->last_name;
            $contact->email         = $request->email;
            $contact->phone         = $request->phone;
            $contact->message       = $request->message;    
            $contact->save();    

            Mail::to('rasarafi5@gmail.com')->send(new ContactMail());

            DB::commit();
            
            return new SuccessResource(code: 201);

        } catch (\Exception $e) {

            DB::rollBack();
            return new FailedResource(500, $e->getMessage());
        }

    }
}
