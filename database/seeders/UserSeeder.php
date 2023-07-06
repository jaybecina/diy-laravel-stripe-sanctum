<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'first_name' => 'DIY',
                'last_name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('Password@123'),
                'address' => '84-106 Ann St, Brisbane City QLD 4000, Australia',
                'mobile_num' => '61212345678',
                'has_moodle' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => NULL,
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'customer@nxt.work',
                'password' => Hash::make('Password@123'),
                'address' => '84-106 Ann St, Brisbane City QLD 4000, Australia',
                'mobile_num' => '61212345678',
                'has_moodle' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => NULL,
            ],
            [
                'first_name' => 'Dawn',
                'last_name' => 'Rosenthal',
                'email' => 'dawn@nxt.work',
                'password' => Hash::make('Password@123'),
                'address' => '84-106 Ann St, Brisbane City QLD 4000, Australia',
                'mobile_num' => '61212345678',
                'has_moodle' => 1,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => NULL,
            ],
        ]);
    }
}
