<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
           ['name' => 'Nursing', 'jp_name' => '介護', 'status' => 1],
           ['name' => 'Building Cleaning', 'jp_name' => 'ビルクリーニング', 'status' => 1],
           ['name' => 'Material industry', 'jp_name' => '素形材産業', 'status' => 1],
           ['name' => 'Machinery manufacturing', 'jp_name' => '産業機械製造業', 'status' => 1],
           ['name' => 'Electrical/electronic information related industry', 'jp_name' => '電気・電子情報関連産業', 'status' => 1],
           ['name' => 'Construction', 'jp_name' => '建設業', 'status' => 1],
           ['name' => 'Lifeline/equipment', 'jp_name' => 'ライフライン・設備区分', 'status' => 1],
           ['name' => 'Ship building/ship industry', 'jp_name' => '造船・舶用業', 'status' => 1],
           ['name' => 'Automobile maintenance', 'jp_name' => '自動車整備業', 'status' => 1],
           ['name' => 'Aviation', 'jp_name' => '航空業', 'status' => 1],
           ['name' => 'Accommodation Industry', 'jp_name' => '宿泊業', 'status' => 1],
           ['name' => 'Agriculture', 'jp_name' => '農業', 'status' => 1],
           ['name' => 'Fishing', 'jp_name' => '漁業', 'status' => 1],
           ['name' => 'Food& beverage manufacturing', 'jp_name' => '飲食料品製造業', 'status' => 1],
           ['name' => 'Restaurant', 'jp_name' => '外食業', 'status' => 1],
           ['name' => 'Sales', 'jp_name' => '営業', 'status' => 1],
           ['name' => 'Finance', 'jp_name' => '経理・財務', 'status' => 1],
           ['name' => 'General affairs/Human resources', 'jp_name' => '総務・人事', 'status' => 1],
           ['name' => 'Financial profession', 'jp_name' => '金融専門職', 'status' => 1],
           ['name' => 'Overseas marketing', 'jp_name' => '海外マーケティング企画', 'status' => 1],
           ['name' => 'Interpretation and translation', 'jp_name' => '通訳・翻訳', 'status' => 1],
           ['name' => 'language teacher', 'jp_name' => '語学教師', 'status' => 1],
           ['name' => 'Designer', 'jp_name' => 'デザイナー', 'status' => 1],
           ['name' => 'Trade', 'jp_name' => '貿易業務', 'status' => 1],
           ['name' => 'Research and Development', 'jp_name' => '研究開発', 'status' => 1],
           ['name' => 'Engineer', 'jp_name' => 'エンジニア', 'status' => 1],
           ['name' => 'Programming', 'jp_name' => 'プログラマー', 'status' => 1],
           ['name' => 'Layout Design', 'jp_name' => '設計', 'status' => 1],
           ['name' => 'Industrial science', 'jp_name' => '生産技術', 'status' => 1],
        ];

        DB::table('jobs_category')->insert($data);
    }
}
