<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * A topic has one to many relationship with subscriptions
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * A topic has one to many relationship with notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
