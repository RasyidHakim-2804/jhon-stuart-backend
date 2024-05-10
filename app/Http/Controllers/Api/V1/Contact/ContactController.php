<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Requests\Api\V1\Contact\CreateRequest;
use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function store(CreateRequest $request)
    {
        $contact = new Contact();
        $contact->first_name    = $request->first_name;
        $contact->last_name     = $request->last_name;
        $contact->email         = $request->email;
        $contact->phone         = $request->phone;
        $contact->message       = $request->message;
        
        $contact->save();
    }
}
