<?php 
    return [
        'required'=>[
            'email' => 'The :attribute must be a valid email address',
            'phone' => 'The :attribute field is required.',
            'password'=>'The :attribute field is required.',
            'password_confirmation'=>'The :attribute field is required.',
            'old_password_check' => 'The current password is incorrect.',
            'old_password'=>'Current password field is required.',
            'country' => 'The :attribute field is required.',
            'country_code'=>'The :attribute field is required.',
            'intro_video'=>'The :attribute field is required.',
            'intro_video_file'=>'The introduction video field must be a video file',
            // 'user_type'=>'The :attribute field is required.',
             'otp'=>'The :attribute field is required.',
             'cancel_reason'=>'The :attribute field is required.',
             'jobseeker'=>[
                'required'=>'The jobseeker field is required.',
                'exists' => 'The selected jobseeker does not exists in the system.',
                'first_name'=> 'The :attribute field is required.',
                'last_name'=> 'The :attribute field is required.',
                'about'=> 'The :attribute field is required.'
             ],
            'gender' => [
                'integer' => 'The gender field is required.',
                'min' => 'The gender field is required.',
                'max' => 'The gender field is required.',
                // 'not_in' => 'The selected gender is invalid.', // Update this message according to your application's context
            ],
            'japanese_level' => [
                'integer' => 'The japanese level field is required.',
                'min' => 'The japanese level field is required.',
                'max' => 'The japanese level field  is required.',
                // 'not_in' => 'The selected japanese level is invalid.', // Update this message according to your application's context
            ],
            'experience' => [
                'integer' => 'The experience field must be Less than 1 year,2 year,3 year or more.',
                'min' => 'The experience field must be Less than 1 year,2 year,3 year or more.',
                'max' => 'The experience field must be Less than 1 year,2 year,3 year or more',
                // 'not_in' => 'The selected experience is invalid.', // Update this message according to your application's context
            ],
            'company'=>[
                'name'=> 'The company name field is required.',
                'about'=>'The company about field is required.',
                'job'=>'The company job field is required.',
                'address'=> 'The company address field is required.'

            ],
            'job_type'=>'Please select Job type',
            'pay_type'=>'Please select Pay type ',
            'pay_type_in' => 'Please select Pay type ',
            'job' => [
            'required' => 'The job field is required.',
            'exists' => 'The selected job does not exists in the system.',
                'title'=>'The job title field is required.',
                'array'=>'The company job field must be an array.',
                'salary_from'=>'The salary from field is required.',
                'required_skills'=>'The required skills field is required.',
                    'occupation'=> [
                        'required'=>'The occupation field is required',
                        'exists'=>'The occupation field  dont exists on system'
                    ],
                    'location' => [
                        'required' => 'The location field is required',
                        'exists' => 'The location field  dont exists on system'
                    ],

            ],
            'tags' => [
                      'exists' => 'The tags field  dont exists on system',
                      'array'=>'The tags field must be an array'
             ],
             'matching_accept'=>[
                'required'=>'The matching accept field is required ',
                'in'=>'Matching accept must be either accept or refuse'
             ],
             'user'=>[
                'required'=>'The user field is required.',
                'exists'=>'The selected user does not exists in the system.'
             ],
             
            
            
            
        ],
        'boolean'=>'The :attribute must be true or false.',
        'email' => 'The :attribute must be a valid email address.',
        'exists' => [
            'email' => 'The selected email does not exists in the system.',
            'phone'=>'The selected phone does not exists in the system.',
            'otp'=>'Invalid OTP code.'
        ],
        'confirmed' => 'The :attribute confirmation does not match.',
        'password' => [
            'min' => 'The :attribute must be at least :min characters.'
        ],
        'unique'=>[
            'phone'=>'This phone number already exists in the system.',
            'email'=> 'This email already exists in the system.'
        ]
      
    ]
?>