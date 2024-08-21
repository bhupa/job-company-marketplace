<?php

namespace App\Http\Controllers\Api\V1;

class OnboardingController extends BaseController
{
    public function index()
    {
        $data = [
            [
                'id' => '1',
                'title' => [
                    'en' => 'Left Swipe',
                    'ja' => '左スワイプ',
                ],
                'description' => [
                    'en' => 'Swipe left or press Cross button to unlike',
                    'ja' => '希望に合わない人は左スワイプまたは×を押すともう表示されません',
                ],
                'image' => env('APP_URL').'onboarding/leftSwipe.gif',
            ],
            [
                'id' => '2',
                'title' => [
                    'en' => 'Right Swipe',
                    'ja' => '右スワイプ',
                ],
                'description' => [
                    'en' => 'Swipe right or press Checkmark button to like',
                    'ja' => '右スワイプまたは✓をすると興味あり！の意思表示が相手に通知されます',
                ],
                'image' => env('APP_URL').'onboarding/rightSwipe.gif',
            ],
            [
                'id' => '3',
                'title' => [
                    'en' => 'Rewind',
                    'ja' => 'プレバック',
                ],
                'description' => [
                    'en' => 'Press rewind button to go back to last left swiped card',
                    'ja' => 'プレイバック：ひとつ前の人に戻ります',
                ],
                'image' => env('APP_URL').'onboarding/rewindJobseeker.gif',
            ],
            [
                'id' => '4',
                'title' => [
                    'en' => 'Bookmark',
                    'ja' => 'ブックマーク',
                ],
                'description' => [
                    'en' => 'Press bookmark button to bookmark',
                    'ja' => '気になる人をお気に入り登録できます',
                ],
                'image' => env('APP_URL').'onboarding/bookmark.gif',
            ],
            [
                'id' => '5',
                'title' => [
                    'en' => 'Direct Chat',
                    'ja' => 'ダイレクトチャット',
                ],
                'description' => [
                    'en' => 'Press Chat Request button to send chat request',
                    'ja' => '気になる人にチャットリクエストを送れます',
                ],
                'image' => env('APP_URL').'onboarding/directChat.gif',
            ],
        ];

        return $this->success($data, 'Onboard content');
    }
}
