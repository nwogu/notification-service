<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Topic;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscribeTest extends TestCase
{
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_that_user_can_subscribe_to_topic()
    {
        $topic = Topic::factory()->create();

        $url = $this->faker->url();
        
        $response = $this->postJson("/subscribe/{$topic->name}", compact('url'));

        $response->assertStatus(201);

        $response->assertJson([
            'url' => $url,
            'topic' => $topic->name
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'url' => $url,
        ]);
    }

    public function test_that_user_cannot_subscribe_to_unknown_topic()
    {
        $url = $this->faker->url();
        $name = $this->faker->name();
        
        $response = $this->postJson("/subscribe/{$name}", compact('url'));

        $response->assertStatus(404);

        $this->assertDatabaseMissing('subscriptions', [
            'url' => $url,
        ]);
    }

    public function test_that_user_cannot_subscribe_without_url()
    {
        $url = '';
        $name = $this->faker->name();
        
        $response = $this->postJson("/subscribe/{$name}", compact('url'));

        $response->assertStatus(422);

        $this->assertDatabaseMissing('subscriptions', [
            'url' => $url,
        ]);
    }
}
