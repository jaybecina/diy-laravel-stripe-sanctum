<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

use App\Models\MoodBoardImage;
use App\Models\User;

class MoodBoardImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Mood Board Images Seed */
        $uid1 = '11164eb89fg123ryi0asd3k3gh3y5g4b8173515686543192968421'; 

        $image_path1 = 'mood_board_images/' . $uid1 . '.png';

        $image1 = "https://www.furniture-republic.com.ph/sites/default/files/204042-perpective-back.jpg";

        $getImageContent1 = file_get_contents($image1);

        if (Storage::exists($image_path1)) {
            unlink($image_path1);
        }

        /*  Handle File Upload */
        // Naming the file
        $imageName1 = $uid1 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_images/' . $imageName1, $getImageContent1);

        MoodBoardImage::create([
            'name'          =>  'Chair Test 1',
            'image_url'     =>  config('app.url') . '/storage/mood_board_images/' . $imageName1,
            'user_id'       =>  User::all()->random()->id,
            'w'             =>  500,
            'h'             =>  500,
        ]);

        $uid2 = '2229bj68jyn7884555kji03x6cv6e523n4hf7asp0p8712316734532365623487651';

        $image_path2 = 'mood_board_images/' . $uid2 . '.png';

        if (Storage::exists($image_path2)) {
            unlink($image_path2);
        }

        $image2 = "https://d2bcmsv4ms9on7.cloudfront.net/catalog/product/cache/5bc9fafbd097fcac6ea36e150ae65605/9/8/986400001557-1_1.jpg";

        $getImageContent2 = file_get_contents($image2);

        /* Handle File Upload */
        // Naming the file
        $imageName2 = $uid2 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_images/' . $imageName2, $getImageContent2);

        MoodBoardImage::create([
            'name'          =>  'Drawer Test 2',
            'image_url'     =>  config('app.url') . '/storage/mood_board_images/' . $imageName2,
            'user_id'       =>  User::all()->random()->id,
            'w'             =>  500,
            'h'             =>  500,
        ]);

        $uid3 = '33352jqwe97656bnjf123543frege3n4f8s5sf3g2jk69873943491387105874645';

        $image_path3 = 'gallery_images/' . $uid3 . '.png';

        if (Storage::exists($image_path3)) {
            unlink($image_path3);
        }

        $image3 = "https://d2bcmsv4ms9on7.cloudfront.net/catalog/product/cache/5bc9fafbd097fcac6ea36e150ae65605/3/1/317300004511-2_1.jpg";

        $getImageContent3 = file_get_contents($image3);

        /* Handle File Upload */
        // Naming the file
        $imageName3 = $uid3 . '.png';

        // Upload Image storage
        Storage::disk('local')->put('/public/mood_board_images/' . $imageName3, $getImageContent3);

        MoodBoardImage::create([
            'name'          =>  'Drawer Test 3',
            'image_url'     =>  config('app.url') . '/storage/mood_board_images/' . $imageName3,
            'user_id'       =>  User::all()->random()->id,
            'w'             =>  500,
            'h'             =>  500,
        ]);
    }
}
