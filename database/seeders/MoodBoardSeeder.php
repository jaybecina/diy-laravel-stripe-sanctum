<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\MoodBoard;
use App\Models\MoodBoardImage;
use App\Models\MoodBoardBackground;
use App\Models\MoodBoardFrame;
use App\Models\User;

class MoodBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $moodBoardName = "Test 1 Lounge Room";
        $moodBoardImgData = [
            [
                'mood_board_image_id'  =>  1,
                'x'                    =>  0,
                'y'                    =>  0,
                'w'                    =>  20,
                'h'                    =>  20,
            ],
            [
                'mood_board_image_id'  =>  2,
                'x'                    =>  50,
                'y'                    =>  0,
                'w'                    =>  50,
                'h'                    =>  50,
            ],
            [
                'mood_board_image_id'  =>  3,
                'x'                    =>  0,
                'y'                    =>  50,
                'w'                    =>  20,
                'h'                    =>  20,
            ],
        ];

        /* Mood Board Seed */
        $mood_board = MoodBoard::create([
            'name'     =>  $moodBoardName,
            'user_id'  =>  2,
            'version'  =>  1,
        ]);

        $mood_board = MoodBoard::where([
            'name'     =>  $moodBoardName, 
            'user_id'  =>  2
        ])->latest()->first();

        foreach($moodBoardImgData as $moodBoardImg) {
            $mood_board->mood_board_images()->attach($moodBoardImg['mood_board_image_id'], 
                [
                    'x'  =>  $moodBoardImg['x'],
                    'y'  =>  $moodBoardImg['y'],
                    'w'  =>  $moodBoardImg['w'],
                    'h'  =>  $moodBoardImg['h'],
                ]
            );
        }

        $mood_board->mood_board_backgrounds()->attach(1);

        $mood_board->mood_board_frames()->attach(1);
    }
}
