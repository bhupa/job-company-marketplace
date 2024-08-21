<?php
return [
    'required' => [
        'email' => '正しいメールアドレス入力してください',
        'phone' => '電話番号は必須項目です',
        'password' => 'パスワードは必須項目です',
        'password_confirmation' => 'パスワード確認は必須項目です',
        'old_password_check' => '現在のパスワードは間違えています',
        'old_password' => '現在のパスワードは必須項目です',
        'country' => '国は必須項目です',
        'country_code' => '国コードは必須項目です',
        // 'user_type' => 'The :attribute field is required.'
        'otp'=>'OTPは必須項目です',
        'cancel_reason' => 'The :attribute field is required.',
        'jobseeker'=>[
                'required'=>'The jobseeker field is required.',
                'exists' => 'The selected jobseeker does not exist in the system.',
                'first_name'=> '名前は必須項目です',
                'last_name'=> '苗字は必須項目です',
                'about'=> '自己PRは必須項目です'
             ],
        'gender' => [
            'integer' => '接別は必須項目です',
            'min' => '接別は必須項目です',
            'max' => '接別は必須項目です',
            // 'not_in' => 'The selected gender is invalid.', // Update this message according to your application's context
        ],
        'japanese_level' => [
            'integer' => '日本語レベルは必須項目です',
            'min' => '日本語レベルは必須項目です',
            'max' => 'T日本語レベルは必須項目です',
            // 'not_in' => 'The selected japanese level is invalid.', // Update this message according to your application's context
        ],
        'experience' => [
            'integer' => '経験は1年未満、2年未満、3年以上になります',
            'min' => '経験は1年未満、2年未満、3年以上になります',
            'max' => '経験は1年未満、2年未満、3年以上になります',
            // 'not_in' => 'The selected experience is invalid.', // Update this message according to your application's context
        ],
        'company' => [
            'name' => '企業名は必須項目です',
            'about' => '企業PRは必須項目です',
            'job' => '仕事は必須項目です',
            'address' => '住所は必須項目です'

        ],
        'job_type' => '雇用形態を選択してください',
        'pay_type' => '給与の種類を選択してください',
        'pay_type_in' => '給与の種類を選択してください',
        'job' => [
            'required' => 'The job field is required.',
            'exists' => 'The selected job does not exist in the system.',
            'title' => 'タイトルは必須項目です',
            'array' => 'The company job field must be an array.',
            'salary_from' => '給与は必須項目です.',
            'required_skills' => 'スキルは必須項目です.',
            'occupation' => [
                'required' => '職種は必須項目です',
                'exists' => '職種はシステムに存在しません'
            ],
            'location' => [
                'required' => '勤務地は必須項目です',
                'exists' => '勤務地は存在しません'
            ],

        ],
        'tags' => [
            'exists' => 'システムにタグが存在しません',
            'array' => 'The tags field must be an array'
        ],
        'matching_accept' => [
            'required' => 'The matching accept field is required ',
            'in' => 'Matching accept must be either accept or refuse'
        ],
        'user' => [
            'required' => 'The user field is required.',
            'exists' => 'The selected user does not exist in the system.'
        ],

    ],
    'email' => '正しいメールアドレス入力してください',
    'exists' => [
        'email' => 'メールアドレスが存在しません.',
        'phone' => '電話番号が存在しません',
        'otp' => 'OTPが間違えています'
    ],
    'confirmed' => 'パスワードの一致しません',
    'password' => [
        'min' => 'パスワードは８桁以上にしてください'
    ],
    'unique' => [
        'phone' => '電話番号はすでに登録されています',
        'email' => 'メールアドレスはすでに登録されています'
    ]
    

]
    ?>