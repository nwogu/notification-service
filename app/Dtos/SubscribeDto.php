<?php

namespace App\Dtos;

use App\Models\Topic;
use Illuminate\Contracts\Support\Arrayable;

class SubscribeDto implements Arrayable
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var App\Models\Topic
     */
    protected $topic;

    public function __construct(string $url, Topic $topic)
    {
        $this->url = $url;
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
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
            'url' => $this->url
        ];
    }
}