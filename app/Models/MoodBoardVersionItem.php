<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MoodBoardItem;

class MoodBoardVersionItem extends Model
{
    use HasFactory;

    public function item(){
        return $this->belongsTo(MoodBoardItem::class,'mood_board_item_id');
    }
}
