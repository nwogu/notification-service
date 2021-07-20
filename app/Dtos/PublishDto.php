<?php

namespace App\Dtos;

use App\Models\Topic;
use App\Models\Notification;
use Illuminate\Contracts\Support\Arrayable;

class PublishDto implements Arrayable
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @var string
     */
    public $status = Notification::IN_PROGRESS;

    /**
     * @var App\Models\Topic
     */

    public function __construct(string $data, Topic $topic)
    {
        $this->data = $data;
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return App\Models\Topic
     */
    public function getTopic(): Topic
    {
        return $this->topic;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'status' => $this->status
        ];
    }
}