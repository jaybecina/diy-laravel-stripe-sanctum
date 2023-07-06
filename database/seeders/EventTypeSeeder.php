<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventType::insert([
            ['name'=>'One on one'],
            ['name'=>'Zoom public'],
            ['name'=>'Zoom private'],
            ['name'=>'In-person event'],
        ]);
    }
}
