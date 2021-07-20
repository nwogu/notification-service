<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Dtos\SubscribeDto;
use App\Http\Requests\Subscribe;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    /**
     * Subscribe to a given topic
     * 
     * @param Illuminate\Http\Request
     * @param App\Services\SubscriptionService $service
     * 
     * @return Illuminate\Http\Response
     */
    public function __invoke(Subscribe $request, SubscriptionService $service)
    {
        $subscription = $service->createSubscription($request->dto());

        return response()->json([
            'url' => $subscription->url,
            'topic' => $subscription->topic->name
        ], 201);
    }
}
