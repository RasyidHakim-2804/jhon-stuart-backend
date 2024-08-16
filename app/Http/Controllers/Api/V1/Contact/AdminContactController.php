<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Resources\Contact\ContactResource;

class AdminContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('craeted_at', 'desc')->paginate(25);
        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact)
    {
        return new ContactResource($contact);
    }
}
