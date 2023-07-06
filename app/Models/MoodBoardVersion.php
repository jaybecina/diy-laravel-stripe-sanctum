<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MoodBoardVersionItem;

class MoodBoardVersion extends Model
{
    use HasFactory;

    public function version_items(){
        return $this->hasMany(MoodBoardVersionItem::class,'mood_board_version_id','id');
    }
}
