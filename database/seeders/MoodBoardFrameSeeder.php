<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\MoodBoardFrame;
use App\Models\User;

class MoodBoardFrameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Mood Board Frame Seed */
        $uid1 = '111877685n34957vgfhjdbh9dffbnv9dx2bnm24884df1vbns1125976468652340945674643'; 

        $image_path1 = 'mood_board_frames/' . $uid1 . '.png';

        $image1 = "https://pngimg.com/uploads/picture_frame/picture_frame_PNG114.png";

        $getImageContent1 = file_get_contents($image1);

        if (Storage::exists($image_path1)) {
            unlink($image_path1);
        }

        /*  Handle File Upload */
        // Naming the file
        $imageName1 = $uid1 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_frames/' . $imageName1, $getImageContent1);

        MoodBoardFrame::create([
            'name'           =>  'Frame Test 1',
            'image_url'      =>  config('app.url') . '/storage/mood_board_frames/' . $imageName1,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);

        $uid2 = '222g9xye56gg67bhuj4nbg8cvn3vgtdyfh34we0hbj6n5se1652565936570366925346698791';

        $image_path2 = 'mood_board_frames/' . $uid2 . '.png';

        if (Storage::exists($image_path2)) {
            unlink($image_path2);
        }

        $image2 = "https://www.pngmart.com/files/22/Photo-Frame-Transparent-Isolated-Images-PNG.png";

        $getImageContent2 = file_get_contents($image2);

        /* Handle File Upload */
        // Naming the file
        $imageName2 = $uid2 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_frames/' . $imageName2, $getImageContent2);

        MoodBoardFrame::create([
            'name'           =>  'Frame Test 2',
            'image_url'      =>  config('app.url') . '/storage/mood_board_frames/' . $imageName2,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);

        $uid3 = '33352jqwe652fhg61brzx89a45b3wv9825b8dt8abnv2823gbhv2g8h23d29076787345343452376583601';

        $image_path3 = 'mood_board_frames/' . $uid3 . '.png';

        if (Storage::exists($image_path3)) {
            unlink($image_path3);
        }

        $image3 = "https://www.pinpng.com/pngs/m/56-568302_rectangle-frame-png-gold-certificate-border-png-transparent.png";

        $getImageContent3 = file_get_contents($image3);

        /* Handle File Upload */
        // Naming the file
        $imageName3 = $uid3 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_frames/' . $imageName3, $getImageContent3);

        MoodBoardFrame::create([
            'name'           =>  'Frame Test 3',
            'image_url'      =>  config('app.url') . '/storage/mood_board_frames/' . $imageName3,
            'mood_board_id'  =>  null,
            'user_id'        =>  null,
            'w'              =>  1000,
            'h'              =>  1000,
            'created_by'     =>  User::all()->random()->id
        ]);
    }
}
