<?php

namespace Database\Seeders;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Event::insert([
            'name'=>'Design session with Jill',
            'description'=>'description...',
            'link'=>'https://us02web.zoom.us/j/84141821955?pwd=ZmdxQ3huTmlzeEp6M2pITzFwaE1uUT09#success',
            'event_schedule'=> Carbon::now()->format('Y-m-d H:i:s'),
            'event_type_id'=> 1,
        ]);
        Event::insert([
            'name'=>'Shopping tour',
            'description'=>'description...',
            'link'=>'https://us02web.zoom.us/j/84141821955?pwd=ZmdxQ3huTmlzeEp6M2pITzFwaE1uUT09#success',
            'event_schedule'=> Carbon::now()->format('Y-m-d H:i:s'),
            'event_type_id'=> 2,
        ]);
        Event::insert([
            'name'=>'Shopping tour with Jay',
            'description'=>'description...',
            'link'=>'https://us02web.zoom.us/j/84141821955?pwd=ZmdxQ3huTmlzeEp6M2pITzFwaE1uUT09#success',
            'event_schedule'=> Carbon::now()->format('Y-m-d H:i:s'),
            'event_type_id'=> 3,
        ]);
        Event::insert([
            'name'=>'Shopping tour with Dave',
            'description'=>'description...',
            'link'=>'https://us02web.zoom.us/j/84141821955?pwd=ZmdxQ3huTmlzeEp6M2pITzFwaE1uUT09#success',
            'event_schedule'=> Carbon::now()->format('Y-m-d H:i:s'),
            'event_type_id'=> 4,
        ]);
    }
}
