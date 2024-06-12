<?php

namespace App\Http\Controllers\Api\V1\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Subscriber\CreateSubscriberRequest;
use App\Http\Resources\Api\V1\Common\SuccessResource;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSubscriberRequest $request)
    {
        $subscriber             = new Subscriber();
        $subscriber->first_name = $request->first_name;
        $subscriber->email      = $request->email;
        $subscriber->save();

        return new SuccessResource(JsonResponse::HTTP_CREATED);
    }
}
