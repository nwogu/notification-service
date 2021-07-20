<?php

namespace App\Services;

use App\Dtos\PublishDto;
use App\Models\Notification;
use App\Jobs\NotifySubscribers;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class PublishService
{
    /**
     * Number of requests to chunk when sending notification
     * @var int
     */
    public const POOL_CHUNK = 500;

    /**
     * Timeout to set in seconds when sending requests
     */
    public const POOL_TIMEOUT = 10;

    /**
     * Create a topic subription
     * @param SubscribeDto $dto
     * 
     * @return App\Models\Notification
     */
    public function publishTopic(PublishDto $dto): Notification
    {
        return tap($dto->getTopic()->notifications()->firstOrCreate($dto->toArray()), function($notification) {
            NotifySubscribers::dispatch($notification);
        });

    }

    /**
     * Notify subscribers
     * @param App\Models\Notification
     * 
     * @return void
     */
    public function notifySubscribers(Notification $notification)
    {
        $topic = $notification->topic;

        $topic->subscriptions()->chunk(self::POOL_CHUNK, function($subscriptions) use ($notification) {
            $this->createResponses(Http::pool(function (Pool $pool) use ($subscriptions, $notification) {
                return $subscriptions->map(function ($subscription) use ($pool, $notification) {
                    return $pool->as($subscription->id)->timeout(self::POOL_TIMEOUT)->acceptJson()->post($subscription->url, [
                        'topic' => $notification->topic->name,
                        'data' => $notification->data
                    ]);
                })->toArray();
            }), $subscriptions, $notification);
        });

        $notification->update(['status' => Notification::COMPLETE]);
    }

    /**
     * Create Notification Responses
     * 
     * @param $responses
     * @param \Illuminate\Support\Collection[App\Models\Subscription]
     * @param App\Models\Notification
     * 
     * @return void
     */
    protected function createResponses($responses, $subscriptions, $notification)
    {
        foreach ($subscriptions as $subscription) {
            $response = $responses[$subscription->id];

            $notification->responses()->create([
                'url' => $subscription->url,
                'status' => $response->successful() ? Notification::SUCCESS : Notification::FAILED,
                'message' => $response->failed() ? $response->toException()->getMessage() : 'successful',
                'response' => $response->body()
            ]);
        }
    }
}