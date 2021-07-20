<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    const SUCCESS = 'success';
    const FAILED = 'failed';
    const COMPLETE = 'complete';
    const IN_PROGRESS = 'in-progress';

    protected $fillable = [
        'topic_id', 'data', 'status'
    ];

    public function responses()
    {
        return $this->hasMany(NotificationResponse::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
    
}
