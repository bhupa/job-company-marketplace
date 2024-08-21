<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = User::where('user_type',2)->get();
        $data = [];
        foreach($users as $user){
             $data[] =[
            'company_name'=>$faker->name,
            'about_company'=>$faker->text(100),
            'address'=>$faker->address,
            'status'=>1,
            'user_id'=> $user->id,
            'logo'=>null
            ];
        }
        DB::table('company')->insert($data);
    }
}
