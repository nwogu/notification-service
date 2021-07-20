<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Publish;
use App\Services\PublishService;

class PublishController extends Controller
{
    /**
     * Publish to a given topic
     * 
     * @param Illuminate\Http\Request
     * @param App\Services\PublishService
     * 
     * @return Illuminate\Http\Response
     */
    public function __invoke(Publish $request, PublishService $service)
    {
        $notification = $service->publishTopic($request->dto());

        return response()->json([
            'status' => $notification->status
        ]);
    }
}
