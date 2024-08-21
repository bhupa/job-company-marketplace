<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['type'=>'jobseeker','name' => 'TOEIC 800 points or more', 'ja_name' => 'TOEIC 800点以上', 'status' => 1],
            ['type'=>'jobseeker','name' => 'Looking for long term', 'ja_name' => '長期滞在希望', 'status' => 1],
            ['type'=>'jobseeker','name' => 'Work visa obtained ', 'ja_name' => '就労VISAあり', 'status' => 1],
            ['type'=>'jobseeker','name' => 'Fluent English speaker', 'ja_name' => '英語話せる', 'status' => 1],
            ['type'=>'jobseeker','name' => 'High motivation', 'ja_name' => 'やる気あり', 'status' => 1],
            ['type'=>'jobseeker','name' => 'High sense of responsibility', 'ja_name' => '責任感あり', 'status' => 1],
            ['type'=>'jobseeker','name' => 'Has working experience', 'ja_name' => '社会人経験あり', 'status' => 1],
            ['type'=>'jobseeker','name' => 'University graduate', 'ja_name' => '大卒', 'status' => 1],
            ['type'=>'jobseeker','name' => 'PC skills available (Word、Excel、PowerPoint)', 'ja_name' => 'PCスキルあり（Word、Excel、PowerPoint）', 'status' => 1],


            ['type'=>'job','name' => 'No experience Ok', 'ja_name' => '未経験OK', 'status' => 1],
            ['type'=>'job','name' => 'Salary increase available', 'ja_name' => '昇給あり', 'status' => 1],
            ['type'=>'job','name' => 'Training available', 'ja_name' => 'トレーニングあり', 'status' => 1],
            ['type'=>'job','name' => 'Educational background not required', 'ja_name' => '学歴不問', 'status' => 1],
            ['type'=>'job','name' => 'Comfortable for Women ', 'ja_name' => '女性活躍', 'status' => 1],
            ['type'=>'job','name' => 'Comfortable for Men ', 'ja_name' => '男性活躍', 'status' => 1],
            ['type'=>'job','name' => 'Urgent Recruitment', 'ja_name' => '急募', 'status' => 1],
            ['type'=>'job','name' => 'Social Insurance available', 'ja_name' => '社保完備', 'status' => 1],
            ['type'=>'job','name' => 'Use English', 'ja_name' => '英語力活用', 'status' => 1],
            ['type'=>'job','name' => 'Providing accomodation', 'ja_name' => '住まい提供', 'status' => 1],
            ['type'=>'job','name' => '2 days off per week', 'ja_name' => '昇給あり', 'status' => 1],
            ['type'=>'job','name' => 'No Uniform', 'ja_name' => '服装自由', 'status' => 1],
            ['type'=>'job','name' => 'Near Station', 'ja_name' => '駅チカ', 'status' => 1],
            ['type'=>'job','name' => 'Skill Up', 'ja_name' => 'スキルアップ', 'status' => 1],
            ['type'=>'job','name' => 'Big Company', 'ja_name' => '大手企業', 'status' => 1],
        ];

        DB::table('tags')->insert($data);
    }
}
