<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Material;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(5)->create();
        Meal::factory()
        ->count(10)
        ->hasAttached(Material::factory()->count(2),['quality' => 2])
        ->create();
        DB::table('admins')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin'),
        ]);
        DB::table('users')->insert([
            'username' => 'user0',
            'password' => Hash::make('123456'),
            'name' => 'Nguyễn Văn Minh',
            'phone' => '0911222333',
        ]);
    }
}
