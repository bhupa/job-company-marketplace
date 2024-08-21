<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\V1\District;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TagSeeder::class,
            DistrictSeeder::class,
            AdminUserSeeder::class,
            CompanySeeder::class,
            JobCategorySeeder::class,
            JobseekerSeeder::class,
            JobSeeder::class,
            // PlanSeeder::class,
          
        ]);
    }
}
