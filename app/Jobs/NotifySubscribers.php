<?php

namespace App\Jobs;

use Exception;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use App\Services\PublishService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class NotifySubscribers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var App\Models\Notification
     */
    protected $notification;

    /**
     * Create a new job instance.
     * 
     * @param App\Models\Notification
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     * 
     * @param App\Services\PublishService
     *
     * @return void
     */
    public function handle(PublishService $service)
    {
        $service->notifySubscribers($this->notification);
    }

    /**
     * Handle a failed job instance
     */
    public function failed(Exception $exception)
    {
        $this->notification->update(['status' => 'failed']);
    }
}
