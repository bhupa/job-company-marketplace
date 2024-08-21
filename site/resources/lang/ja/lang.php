<?php
return [
    'register_id' => 'リクエスターID',
    'name' => '名前',
    'phone' => '電話番号',
    'email' => 'メールアドレス',
    'chat_room_id' => 'チャットルームID',
    'not_found' => ':name, 見つかりませんでした',
    'errors' => [
        'invalid_credentials' => '無効な資格情報',
        'internal' => '内部サーバーエラー',
    ],
    'exists' => [
        'already' => ':name, すべに登録されています',
        'not' => ':name, 存在しません'
    ],
    'user' => [
        'total_otp' => 'OTPの合計数',
        'total_max_opt' => 'OTPの制限に達しました',
        'detail' => 'ユーザーの詳細',
        'login_successful' => '正常にログアウトしました',
        'profile_violation' => 'あなたのアカウントを禁止されました',
        'register_successfully' => 'ユーザー登録が完了しました',
        'logout_successfully' => '正常にログアウトしました',
        'status_update' => 'ユーザーステータスが正常に更新されました',
        'device_token_refresh' => 'ユーザーのデバイストークンが正常に更新されました',
        'max_otp' => '最大のOTPが使用されました！明日もう一度お試しください.',
         'check_email'=>'メールを確認してください',
            'password'=>[
                'reset'=>'パウワード更新しました',
                'changed'=>'Password changed successfully'
            ],
            'email'=>[
                'verified'=>'メールアドレスは確認されました',
                'verify' => 'メールアドレスはすでに確認されました',
                'changed'=>'メールアドレスを更新しました'
            ],
            'account'=>[
                'document_submit'=>'アカウント確認書類が正常に提出されました'
            ],
            
    ],
    'chat' => [
        'limit' => ' 月間ダイレクトチャットの利用制限に達しました',
        'limit_month' => '月間利用制限に達しました！'
    ],
    'invalid_otp' => 'OTPが間違えています',
    
    'notification' => [
        'chat' => [
            'title' => '新しいチャットメッセージ',
            'body' => 'から新メッセージがあります。'
        ],
        'match' => [
            'title' => 'Matched',
            'body' => '新マッチがあります。'
        ],
        'chat-request' => [
            'title' => 'チャットリクエスト',
            'body' => 'からチャットリクエストがあります。'
        ],
        'chat-request-matched' => [
            'title' => 'Matched',
            'body' => 'がチャットリクエストを承諾しました'
        ],
        'account-verification' => [
            'title' => 'Account Verification',
            'body' => 'アカウントは確認されました'
        ],
        'account-rejected' => [
            'title' => 'Account  Rejected',
            'body' => "アカウントを確認できませんでした。元の書類を提出してください"
        ],
        'voilation-approve' => [
            'title' => 'Voilation Approve',
            'body' => "Tご報告ありがとうございます。対処し、アカウントを禁止しました"
        ],
        'voilation-rejected' => [
            'title' => 'Voilation Rejected',
            'body' => "当社のコミュニティガイドラインに違反していません。"
        ],
        'unrestricted-chat' => [
            'title' => '本マッチング',
            'body' => "本マッチング開始しました。"
        ]
    ],
    'subscription_email_template' => [

        'subscription_start' => [
            'heading' => 'Payment Confirmation for JOBMATCHY',
            'title' => 'この度は、JOBMATCHYへご入金いただき、誠にありがとうございます。',
            'description' => '以下のご契約についての入金を確認させていただきましたので
                 ご案内いたします。',
            'name' => 'お名前',
            'contract_details' => 'ご契約の内容',
            'contract_details_text' => 'JOBMATCHY利用料',
            'contract_period' => 'ご契約期間',
            'payment_method' => '支払方法',
            'payment_amount' => 'ご入金額（10％対象',
            'payment_confirmation_date' => '入金確認日',
            'issuer' => '適格請求書発行事業者名',
            'issuer_text' => 'Our Works株式会社',
            'auto_renew' => 'なお、自動更新でお支払いいただいている場合、次回以降のご利用料金は、契約終了月に自動で引き落とされます。',
            'auto_renew_stop' => '自動更新を停止したい場合は、
                 設定よりお手続きをお願いいたします。',
            'pcVersion' => 'PC版',
            'email_sent' => 'このメールは、JOBMATCHY',
            'customized_URL' => 'カスタマイズURL',
            'inquiry_form_text' => 'より送信されています。
                 　お心当たりのない場合やご不明な点がある場合は、お問い合わせフォームよりご連絡ください。',
            'inquiry_form' => 'お問い合わせフォーム'



        ],
    ],
    'chat' => [
        'unrestricted' => [
            'envelope_title' => '本マッチング',
            'page_heading' => '本マッチング機能への支払い',
            'page_description' => '本マッチング決済',
            'page_title' => '下記のユーザーIDからアドミンアシスタントのリクエストがありました。',
            'bank_from_ja' => '日本側口座',
            'bank_from_np' => 'ネパール側口座',
            'bank_name' => '銀行名',
            'account_type' => '口座の種類',
            'account_number' => '口座番号',
            'account_name' => 'アカウント名'
        ]

    ],
    'dear_admin' => 'アドミンさん',
    'best_regards' => 'よろしくお願いします。',
    'include' => '税込',
    'subscribe' => '購読する',
    'plan' => 'プラン',
    'payment_link' => 'Payment Link',
   'account_cancellation'=>[
        'title_one' => '退会理由にチェックを入れてください。',
        'dissatisfaction_with_service_quality'=> 'サービスの品質に不満がある',
        'high_fees'=>'料金が高い',
        'poor_support'=>'サポートが悪い',
        'poor_email_response'=>'メール対応が悪い',
        'lack_of_contact_from_the_other_party' => '相手から連絡がない',
        'poor_technical_support' => '技術サポートが悪い',
        'complicated_payment_procedures' => '支払手続きが面倒',
        'low_frequency_of_use' => '使用頻度が低い',
        'experienced_troubles'=>'トラブルがあった',
        'other'=>'その他',
        'title_two' => '利用料が安ければご継続を考えられますか？',
        'considering'=> '考える',
        'willing_to_consider_depending_on_the_price'=>'価格によって考えても良い',
        'not_considering'=>'考えない',
        'title_three' => '今後の予定を教えてください。',
        'use_other_service'=>'他のサイトへの乗換',
        'continue_working_in_current_company'=>'自社で運用',
         'title_four'=>'お客さまにとって必要なサービスや本サービスへのコメントを自由にお聞かせ下さい'
   ],
    'account_delete' => [
        'title' => 'Your account has been deleted',
        'descripton' => 'We want to ensure that all aspects of your subscription are properly managed. If you have any active subscriptions with automatic renewal, please take a moment to cancel them to avoid any future charges. You can do this by following these steps:',
        'for_ios' => 'For iOS',
        'for_ios_1' => 'Open the App Store',
        'for_ios_2' => 'Go to Account Settings.',
        'for_ios_3' => 'Select Purchase History',
        'for_ios_4' => 'Cancel your subscription.',
        'for_android' => 'For Android',
        'for_android_1' => 'Open the Play Store',
        'for_android_2' => 'Go to Account Settings',
        'for_android_3' => 'Select Payments and Subscriptions.',
        'for_android_4' => 'Cancel your subscription',
    ]


];

?>