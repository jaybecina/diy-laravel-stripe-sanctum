<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function user_events(){
        return $this->hasMany(UserEvent::class,'event_id','id');
    }

    public function event_type(){
        return $this->belongsTo(EventType::class,'event_type_id');
    }
}
