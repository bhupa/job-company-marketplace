<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobCategory;
use App\Models\Jobs;
use App\Models\Jobseeker;
use App\Models\User;
use App\Models\V1\District;
use App\Models\V1\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FakeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createJobseekers();
        $this->createCompaniesWithJobs();
    }
    
    private function createJobseekers()
    {
        for ($i = 0; $i < 25; $i++) {
            try {
                $this->createJobseeker();
            } catch (QueryException $e) {
                $this->handleQueryException($e);
            }
        }
    }
    
    private function createJobseeker()
    {
        $faker = Faker::create();
        $email = $faker->unique()->safeEmail;
        $user = User::factory()->create([
            'email' => $email,
            'user_type' => 1,
            'password' => bcrypt('12345678'),
        ]);
        $jobCategoryId = JobCategory::pluck('id')->toArray();
        $jobseekerTags = Tag::where('type', 'jobseeker')->pluck('id')->toArray();
        $jobseekerData = [
            'user_id' => $user->id,
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'birthday' => now()->subYears(rand(18, 60))->subMonths(rand(0, 11))->subDays(rand(0, 30)),
            'gender' => rand(1, 3),
            'experience' => rand(1, 4),
            'occupation' => $faker->randomElement($jobCategoryId),
            'japanese_level' => rand(1, 5),
            'country' => $faker->country,
            'current_country' => $faker->countryCode,
            'about' => $faker->text(300),
            'paymnet' =>'hour',
        ];
        $jobseeker = Jobseeker::create($jobseekerData);
        $jobseeker->tags()->sync($jobseekerTags);
    }
    
    private function createCompaniesWithJobs()
    {
        for ($i = 0; $i < 25; $i++) {
            try {
                $this->createCompanyWithJobs();
            } catch (QueryException $e) {
                $this->handleQueryException($e);
            }
        }
    }
    
    private function createCompanyWithJobs()
    {
        $faker = Faker::create();
        $email = $faker->unique()->safeEmail;
        $user = User::factory()->create([
            'email' => $email,
            'user_type' => 2,
            'password' => bcrypt('12345678'),
        ]);
        $companyData = [
            'company_name' => $faker->name,
            'about_company' => $faker->text(100),
            'address' => $faker->address,
            'status' => 1,
            'user_id' => $user->id,
            'logo' => null
        ];
        $company = Company::create($companyData);
        $this->createJobsForCompany($company);
    }
    
    private function createJobsForCompany($company)
    {
        $faker = Faker::create();
        $jobCategoryId = JobCategory::pluck('id')->toArray();
        $jobTags = Tag::where('type', 'job')->pluck('id')->toArray();
        $salaryFrom = $faker->numberBetween(100000, 500000);
        $salaryTo = $faker->numberBetween($salaryFrom, 500000);
        $age_from = $faker->numberBetween(18, 55);
        $ageTo = $faker->numberBetween($age_from, 40);
        $publishedDate = $faker->dateTimeBetween('now', '+15 days');
        foreach (range(1, 5) as $index) {
            $jobData = [
                'user_id' => $company->user_id,
                'job_title' => $faker->text(100),
                'occupation' => $faker->randomElement($jobCategoryId),
                'salary_from' => $salaryFrom ,
                'salary_to' => $faker->numberBetween($salaryFrom, 500000),
                'working_hours' => random_int(1, 3),
                'age_from' => $faker->numberBetween(18, 55),
                'age_to' => $faker->numberBetween($age_from, 40),
                'required_skills' => $faker->text(300),
                'published' => $faker->dateTimeBetween('now', '+15 days'),
                'gender' => rand(1, 3),
                'experience' => rand(1, 4),
                'japanese_level' => rand(1, 5),
                'status' => 1,
            ];
            $job = Jobs::create($jobData);
            $job->tags()->sync($jobTags);
        }
    }
    
    private function handleQueryException($e)
    {
        if ($e->errorInfo[1] === 1062) {
            // Duplicate entry error occurred, handle it here
            return true; // Indicate that the exception was handled
        } else {
            // Other query exception occurred, handle it accordingly
            throw $e;
        }
    }

}
