<?php

namespace App\Http\Controllers\Api\V1\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Http\Resources\Subscriber\SubscriberResource;
use Illuminate\Http\Request;

class AdminSubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscribers = Subscriber::paginate(25);

        return SubscriberResource::collection($subscribers);
    }
}
