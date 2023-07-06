<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateBoard extends Model
{
    use HasFactory;

    public function mood_board(){
        return $this->belongsTo(MoodBoard::class,'mood_board_id');
    }
}
