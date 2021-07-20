<?php

namespace App\Services;

use App\Dtos\SubscribeDto;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Create a topic subription
     * @param SubscribeDto $dto
     * 
     * @return App\Models\Subscription
     */
    public function createSubscription(SubscribeDto $dto): Subscription
    {
        return $dto->getTopic()->subscriptions()->firstOrCreate($dto->toArray());
    }
}