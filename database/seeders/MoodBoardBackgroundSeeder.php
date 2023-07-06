<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\MoodBoardBackground;
use App\Models\User;

class MoodBoardBackgroundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Mood Board Background Seed */
        $uid1 = '111e7df6guh23ngu8bnn6yedxun32gicusz6z67xzh3661547894763224986877952334353683645'; 

        $image_path1 = 'mood_board_backgrounds/' . $uid1 . '.png';

        $image1 = "https://images.pexels.com/photos/1287142/pexels-photo-1287142.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1";

        $getImageContent1 = file_get_contents($image1);

        if (Storage::exists($image_path1)) {
            unlink($image_path1);
        }

        /*  Handle File Upload */
        // Naming the file
        $imageName1 = $uid1 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_backgrounds/' . $imageName1, $getImageContent1);

        MoodBoardBackground::create([
            'name'           =>  'Background Test 1',
            'image_url'      =>  config('app.url') . '/storage/mood_board_backgrounds/' . $imageName1,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);

        $uid2 = '222u678yj5jg8fh3ny7vbnmj4m6fg7yrfm4iyvguio4iym16734y748760834452341956135785439712397653474';

        $image_path2 = 'mood_board_backgrounds/' . $uid2 . '.png';

        if (Storage::exists($image_path2)) {
            unlink($image_path2);
        }

        $image2 = "https://images.pexels.com/photos/1831234/pexels-photo-1831234.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1";

        $getImageContent2 = file_get_contents($image2);

        /* Handle File Upload */
        // Naming the file
        $imageName2 = $uid2 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_backgrounds/' . $imageName2, $getImageContent2);

        MoodBoardBackground::create([
            'name'           =>  'Background Test 2',
            'image_url'      =>  config('app.url') . '/storage/mood_board_backgrounds/' . $imageName2,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);

        $uid3 = '333b8972345yig8q28tbv283vh9ajk523gni2345b687w3tyn29tgh987wng6y90o27685978615646t785013634';

        $image_path3 = 'mood_board_backgrounds/' . $uid3 . '.png';

        if (Storage::exists($image_path3)) {
            unlink($image_path3);
        }

        $image3 = "https://images.pexels.com/photos/3006340/pexels-photo-3006340.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1";

        $getImageContent3 = file_get_contents($image3);

        /* Handle File Upload */
        // Naming the file
        $imageName3 = $uid3 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_backgrounds/' . $imageName3, $getImageContent3);

        MoodBoardBackground::create([
            'name'           =>  'Background Test 3',
            'image_url'      =>  config('app.url') . '/storage/mood_board_backgrounds/' . $imageName3,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);
    }
}
