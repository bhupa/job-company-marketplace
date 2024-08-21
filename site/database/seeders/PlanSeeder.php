<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'One Week',
                'slug' => 'one-week',
                'stripe_plan' => 'prod_Oj1CEhW3VW3TrE',
                'price' => 5,
                'description' => 'One Week',
            ],
            [
                'name' => 'Two Week',
                'slug' => 'two-week',
                'stripe_plan' => 'prod_Oj1Eix3drqviKu',
                'price' => 10,
                'description' => 'Two Week',
            ],
            [
                'name' => 'One months',
                'slug' => 'one-months',
                'stripe_plan' => 'prod_Oj1eVGrBP1ga4g',
                'price' => 10,
                'description' => 'One Months',
            ],
            [
                'name' => 'Three Months',
                'slug' => 'three-months',
                 'stripe_plan' => 'prod_Oj1OTsDgWQXhrB',
                'price' => 30,
                'description' => 'Three Months',
             ],
             [
                'name' => 'Six Months',
                'slug' => 'six-months',
                    'stripe_plan' => 'prod_Oj1V3Xo2nVVNtF',
                'price' => 50,
                'description' => 'Six Months',
             ],
         ];

        Plan::insert($data);
    }
}
