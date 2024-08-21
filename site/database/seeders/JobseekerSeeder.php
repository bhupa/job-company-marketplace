<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use App\Models\User;
use App\Traits\JobTraits;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobseekerSeeder extends Seeder
{
    use JobTraits;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::where('user_type', 1)->get();
        $jobCategoryId = JobCategory::get()->pluck('id')->toArray();
        $data = [];
        foreach ($users as $key => $user) {
            $data[] = [
                'user_id' => $user->id,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                // 'image'=>null,
                'birthday' => now()->subYears(rand(18, 60))->subMonths(rand(0, 11))->subDays(rand(0, 30)),
                'gender' => rand(1, 3),
                'experience' => rand(1, 4),
                'occupation' => $faker->randomElement($jobCategoryId),
                'japanese_level' => rand(1, 5),
                'country' => $faker->country,
                'current_country' => $faker->countryCode,
                'about' => $faker->text(300),
            ];
        }
        DB::table('jobseekers')->insert($data);
    }
}
