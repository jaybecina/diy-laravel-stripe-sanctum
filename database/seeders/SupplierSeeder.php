<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\User;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
            'company' => 'Test QWERTY Furnitures',
            'status' => 'Active',
            'created_by' => User::all()->random()->id
        ]);
        Supplier::create([
            'company' => 'Test 2 ABC Furnitures',
            'status' => 'Active',
            'created_by' => User::all()->random()->id
        ]);
        Supplier::create([
            'company' => 'Test 3 XYZ Furnitures',
            'status' => 'Active',
            'created_by' => User::all()->random()->id
        ]);
        Supplier::create([
            'company' => 'Test 4 Seventeen Furnitures',
            'status' => 'Active',
            'created_by' => User::all()->random()->id
        ]);
        Supplier::create([
            'company' => 'Test 5 Shelby Furnitures',
            'status' => 'Active',
            'created_by' => User::all()->random()->id
        ]);
    }
}
