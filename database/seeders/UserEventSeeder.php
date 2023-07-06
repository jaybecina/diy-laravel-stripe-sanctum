<?php

namespace Database\Seeders;

use App\Models\UserEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserEvent::insert([
            'event_id'=>1,
            'user_id'=>2
        ]);
        UserEvent::insert([
            'event_id'=>2,
            'user_id'=>2
        ]);
        UserEvent::insert([
            'event_id'=>3,
            'user_id'=>2,
            'is_booked'=>1
        ]);
    }
}
