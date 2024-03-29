<?php

namespace App\Models;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $table = 'event_type';
    protected $primaryKey = 'event_type_id';

    protected $fillable = [
        'event_type_name'
    ];
    public function events()
    {
        return $this->hasMany(\App\Models\Events::class, 'event_type_id');
    }
}
