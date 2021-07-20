# Notification Service

A simple notification service based on pub/sub architecture.

## Requirements

- Mysql
- Composer
- PHP >= 7

## Setting Up

- Clone this repo
- Add a .env file to root of project with valid database credentials
- Run ```composer install```
- Run ```php artisan migrate```
- Run ```php artisan db:seed```
- Run ```php artisan migrate```

## Testing

For testing, run ```php artisan test```

## System Architecture

![alt text](pubsub.png?raw=true)

This application was  built using a "**Request -> Validation -> DTO -> Service -> Response**" pattern

HTTP requests are firstly validated in a form request. After which a **data transfer object** is created
from the request. A service class, receives the DTO and acts on it, returning an optional value which may
then be sent back as a response.

### Database Schema
There are 4 entities used to persist data for the entirety of the pub/sub process

- Topic:- This identifies a given topic by name. Running the setup commands inserts two topics by default:
**topic1** and **topic2**

- Subscription:- This identifies the subcriber by url and associates the subscriber to a topic.

- Notification:- This entity represents a single publish event to be broadcast to all subscribers. It also keeps track of the status of all broadcasts

- Notifcation Response:- This entity represents a single status feedback from the subscriber after a notifcation has been broadcasted.

### Jobs

The NotifySubscribers Job is dispatched when data has been published to a topic. This is done so as to
prevent the server from handling external http requests calls within the request cycle. It's a very good
approach especially when dealing with large numbers of subscribers.

### Notifying Subscribers

The ```notifySubscribers``` method of the publish service does quite a number of things

```php
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
```

This method receives a notification object as an argumnent and attempts to notify all the subscribers to its topic. 
The chunk method which is being called on the subscription's query builder prevents us from storing all the subscriptions in memory while pushing the request. Its a memory effecient way to handle the edge case of voluminous data. 
The Http Pool method allows us to make different HTTP requests asynchronously, which means we dont need to wait on one request to finish before another is being called. Its a good use of time. 
Combining both chunk and pool allows us to effectively make use of space/time during the entire operation. A timeout is also set (Defaults to 10 seconds) for each request so we dont spend too much time on it.

After all subscriptions have been notified, we store the status of the response in a NotificationResponse. This allows us to be able to keep track, search or query the individual status of each notified subscriber for whatever reason.
