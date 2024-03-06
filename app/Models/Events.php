<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;
    protected $table = 'events';
    protected $primaryKey = 'event_id';

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }
}
