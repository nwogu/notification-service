<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'url', 
        'notification_id', 
        'status', 
        'message',
        'response'
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
