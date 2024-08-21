<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()
              ->times(20)
              ->create([
                  'user_type' => 1,
                  'password' => bcrypt('12345678'),
              ]);

        \App\Models\User::factory()
        ->times(20)
        ->create([
            'user_type' => 2,
            'password' => bcrypt('12345678'),
        ]);

        // \App\Models\User::factory()->create([
        //     'email' => 'test@example.com',
        //     'country_code' => '977',
        //     'phone' => '9851226794',
        //     'password' => bcrypt('123'),
        //     'user_type' => '1',
        // ]);
    }
}
