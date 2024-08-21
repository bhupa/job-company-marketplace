<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['parent_id'=>null,'name' => 'Hokkaido', 'ja_name' => '北海道', 'status' => 1],
            ['parent_id'=>null,'name' => 'Tohoku', 'ja_name' => '東北', 'status' => 1],
            ['parent_id'=>null,'name' => 'Kanto', 'ja_name' => '関東', 'status' => 1],
            ['parent_id'=>null,'name' => 'Chubu', 'ja_name' => '中部', 'status' => 1],
            ['parent_id'=>null,'name' => 'Kansai', 'ja_name' => '関西', 'status' => 1],
            ['parent_id'=>null,'name' => 'Chugoku', 'ja_name' => '中国', 'status' => 1],
            ['parent_id'=>null,'name' => 'Shikoku', 'ja_name' => '四国', 'status' => 1],
            ['parent_id'=>null,'name' => 'Kyushu', 'ja_name' => '九州', 'status' => 1],
            ['parent_id'=>1,'name' => 'Hokkaido', 'ja_name' => '北海道', 'status' => 1],
            ['parent_id'=>2,'name' => 'Aomori', 'ja_name' => '青森県', 'status' => 1],
            ['parent_id'=>2,'name' => 'Iwate', 'ja_name' => '岩手県', 'status' => 1],
            ['parent_id'=>2,'name' => 'Miyagi', 'ja_name' => '宮城県', 'status' => 1],
            ['parent_id'=>2,'name' => 'Akita', 'ja_name' => '秋田県', 'status' => 1],
            ['parent_id'=>2,'name' => 'Yamagata', 'ja_name' => '山形県', 'status' => 1],
            ['parent_id'=>2,'name' => 'Fukushima', 'ja_name' => '福島県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Ibaraki', 'ja_name' => '茨城県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Tochigi', 'ja_name' => '栃木県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Gunma', 'ja_name' => '群馬県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Saitama', 'ja_name' => '埼玉県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Chiba', 'ja_name' => '千葉県', 'status' => 1],
            ['parent_id'=>3,'name' => 'Tokyo', 'ja_name' => '東京都', 'status' => 1],
            ['parent_id'=>4,'name' => 'Kanagawa', 'ja_name' => '神奈川県', 'status' => 1],
            ['parent_id'=>4,'name' => 'Niigata', 'ja_name' => '新潟県', 'status' => 1],
            ['parent_id'=>4,'name' => 'Toyama', 'ja_name' => '富山県', 'status' => 1],
            ['parent_id'=>4,'name' => 'Ishikawa', 'ja_name' => '石川県', 'status' => 1],        
            ['parent_id'=>4,'name' => 'Fukui', 'ja_name' => '福井県', 'status' => 1],
            ['parent_id'=>4,'name' => 'Yamanashi', 'ja_name' => '山梨県', 'status' => 1],
            ['parent_id'=>4,'name' => 'Nagano', 'ja_name' => '長野県', 'status' => 1],
             ['parent_id'=>4,'name' => 'Gifu', 'ja_name' => '岐阜県', 'status' => 1],
             ['parent_id'=>4,'name' => 'Shizuoka', 'ja_name' => '静岡県', 'status' => 1],
             ['parent_id'=>4,'name' => 'Aichi', 'ja_name' => '愛知県', 'status' => 1],

             ['parent_id'=>5,'name' => 'Mie', 'ja_name' => '三重県', 'status' => 1],
             ['parent_id'=>5,'name' => 'Shiga', 'ja_name' => '滋賀県', 'status' => 1],
             ['parent_id'=>5,'name' => 'Kyōto', 'ja_name' => '京都府', 'status' => 1],
             ['parent_id'=>5,'name' => 'Ōsaka', 'ja_name' => '大阪府', 'status' => 1],
             ['parent_id'=>5,'name' => 'Hyōgo', 'ja_name' => '兵庫県', 'status' => 1],
             ['parent_id'=>5,'name' => 'Nara', 'ja_name' => '奈良県', 'status' => 1],
             ['parent_id'=>5,'name' => 'Wakayama', 'ja_name' => '和歌山県', 'status' => 1],


             ['parent_id'=>6,'name' => 'Tottori', 'ja_name' => '鳥取県', 'status' => 1],
             ['parent_id'=>6,'name' => 'Shimane', 'ja_name' => '島根県', 'status' => 1],
             ['parent_id'=>6,'name' => 'Okayama', 'ja_name' => '岡山県', 'status' => 1],
             ['parent_id'=>6,'name' => 'Hiroshima', 'ja_name' => '広島県', 'status' => 1],
             ['parent_id'=>6,'name' => 'Yamaguchi', 'ja_name' => '山口県', 'status' => 1],
             
             ['parent_id'=>7,'name' => 'Tokushima', 'ja_name' => '徳島県', 'status' => 1],
             ['parent_id'=>7,'name' => 'Kagawa', 'ja_name' => '香川県', 'status' => 1],
             ['parent_id'=>7,'name' => 'Ehime', 'ja_name' => '愛媛県', 'status' => 1],
             ['parent_id'=>7,'name' => 'Kochi', 'ja_name' => '高知県', 'status' => 1],


             ['parent_id'=>8,'name' => 'Fukuoka', 'ja_name' => '福岡県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Saga', 'ja_name' => '佐賀県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Nagasaki', 'ja_name' => '長崎県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Kumamoto', 'ja_name' => '熊本県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Oita', 'ja_name' => '大分県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Miyazaki', 'ja_name' => '宮崎県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Kagoshima', 'ja_name' => '鹿児島県', 'status' => 1],
             ['parent_id'=>8,'name' => 'Okinawa', 'ja_name' => '沖縄県', 'status' => 1],
        ];
         DB::table('districts')->insert($data);
    }
}
