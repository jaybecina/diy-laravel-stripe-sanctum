<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        // events seeder
        $this->call(EventSeeder::class);
        $this->call(UserEventSeeder::class);
        $this->call(EventTypeSeeder::class);
        // plans seeder
        $this->call(PlanSeeder::class);
        // supplier seeder
        $this->call(SupplierSeeder::class);
        $this->call(MoodBoardItemsSeeder::class);
        // $this->call(MoodBoardBackgroundSeeder::class);
        // $this->call(MoodBoardFrameSeeder::class);
        // $this->call(MoodBoardImageSeeder::class);
        // $this->call(MoodBoardSeeder::class);
        Model::reguard();
    }
}
