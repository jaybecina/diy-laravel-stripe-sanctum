<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodBoard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'frame_color',
        'frame_background',
        'image_placeholder',
    ];

    /**
     * The images that belong to the moodboard.
     */
    
     public function versions(){
        return $this->hasMany(MoodBoardVersion::class,'mood_board_id','id');
     }
}
