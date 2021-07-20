<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Topic;
use App\Models\Notification;
use App\Models\Subscription;
use App\Jobs\NotifySubscribers;
use App\Services\PublishService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublishTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_that_user_can_publish_to_topic()
    {
        $topic = Topic::factory()->create();

        $response = $this->postJson("/publish/{$topic->name}", [
            'tuna' => $this->faker->word()
        ]);

        $response->assertStatus(200);
        
        $response->assertJson([
            'status' => 'in-progress'
        ]);
    }

    public function test_that_notification_is_dispatched_when_topic_is_published()
    {
        Bus::fake();
        
        $topic = Topic::factory()->create();

        $response = $this->postJson("/publish/{$topic->name}", [
            'queuable' => $this->faker->word()
        ]);

        Bus::assertDispatched(NotifySubscribers::class);

    }

    public function test_that_subscribers_are_notified()
    {
        Http::fake([
            'github.com' => Http::response(['foo' => 'bar'], 200),
            'google.com' => Http::response([], 404)
        ]);

        $topic = Topic::factory()->create();

        $subscription1 = Subscription::factory()->for($topic)->create([
            'url' => 'http://google.com'
        ]);
        $subscription2 = Subscription::factory()->for($topic)->create([
            'url' => 'http://github.com'
        ]);

        $notification = Notification::factory()->for($topic)->create();

        $service = $this->app->make(PublishService::class);

        $service->notifySubscribers($notification);

        $this->assertTrue($notification->responses()->count() == 2);
        $this->assertTrue(
            $notification->responses()->whereUrl($subscription1->url)->first()->status == 'failed'
        );
        $this->assertTrue(
            $notification->responses()->whereUrl($subscription2->url)->first()->status == 'success'
        );
    }
}
