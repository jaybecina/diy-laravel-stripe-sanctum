<?php

namespace Database\Seeders;

use App\Models\MoodBoardItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MoodBoardItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        MoodBoardItem::insert([
            [
                "id"=> 1,
                "name"=> "Sample Board Item 1",
                "image_url"=> "https://www.furniture-republic.com.ph/sites/default/files/204042-perpective-back.jpg",
                "user_id"=>2
            ],
            [
                "id"=> 2,
                "name"=> "Sample Board Item 2",
                "image_url"=> "https://d2bcmsv4ms9on7.cloudfront.net/catalog/product/cache/5bc9fafbd097fcac6ea36e150ae65605/9/8/986400001557-1_1.jpg",
                "user_id"=>2
            ],
            [
                "id"=> 3,
                "name"=> "Sample Board Item 3",
                "image_url"=> "https://d2bcmsv4ms9on7.cloudfront.net/catalog/product/cache/5bc9fafbd097fcac6ea36e150ae65605/3/1/317300004511-2_1.jpg",
                "user_id"=>2
            ]
        ]);
    }
}
