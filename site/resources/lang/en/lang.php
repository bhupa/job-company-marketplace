<?php 
    return [
        'register_id' => 'REQUESTER ID',
        'name'=>'NAME',
        'phone'=>'PHONE',
        'email'=>'Email',
        'chat_room_id'=>'CHAT ROOM ID',
        'not_found'=>':name, not found',
        'errors'=>[
            'invalid_credentials'=>'Invalid credentials!',
            'internal'=>'Internal Server Error',
        ],
        'exists'=>[
            'already'=>':name, already exists',
            'not'=>':name, not exists'
        ],
        'user'=>[
            'details'=>'User Details',
            'total_otp'=>'Total number of user otp',
            'total_max_opt'=>'OTP limit reached',
            'detail'=>'User details',
            'login_successful'=>'Login Successful',
            'profile_violation'=>'Your account has been banned',
            'register_successfully'=>'User register sucessfully',
            'logout_successfully'=>'Logout successfully',
            'status_update'=>'User status updated sucessfully',
            'device_token_refresh'=>'User device token updated sucessfully',
            'max_otp'=>'Maximum OTP used! Please try again tomorrow.',
            'check_email'=>'Check your email',
            'password'=>[
                'reset'=>'Password reset successfully',
                'changed'=>'Password changed successfully'
            ],
            'email'=>[
                'verified'=>'Email already verified',
                'verify' => 'Email has been verified',
                'changed'=>'Email has been changed'
            ],
            'account'=>[
                'document_submit'=>'Account verify document submit successfuly'
            ],
            
        ],
        'chat'=>[
            'limit'=>' You have reached monthly direct chat limit',
            'limit_month'=>'You have reached limit for the month!'
        ],
        'invalid_otp'=>'Invalid otp',
        
       'notification'=>[
            'chat'=>[
                'title'=>'New chat message',
                'body'=>'has sent new message'
            ],
            'match'=>[
                'title'=>'Matched',
                'body'=>'You have a new match'
            ],
            'chat-request'=>[
                'title'=>'Chat Request',
                'body'=>'has send you chat request'
            ],
            'chat-request-matched'=>[
            'title' => 'Matched',
            'body' => 'has accepted your chat request'
            ],
            'account-verification'=>[
                'title'=>'Account Verification',
                'body'=>'Your account has been verified'
            ],
            'account-rejected'=>[
                'title'=>'Account  Rejected',
                'body'=>"We cound't verify your account, Please submit original documents"
            ],
            'violation-approve'=>[
                'title'=>'Voilation Approve',
                'body'=>"Thank you for reporting, we took action and banned the account"
            ],
            'violation-rejected'=>[
                'title'=>'Voilation Rejected',
                'body'=>"Thank you for reporting, the account you have reported doesn't violate our community guidance"
            ],
            'unrestricted-chat'=>[
            'title'=>'Unrestricted Chat',
            'body'=>"Unrestricted chat is enabled "
            ]
        ],
        'subscription'=>[
            'jobseeker'=>[
                'ios'=> [
                    'products'=> [
                        'jobseeker_one_week'=>[
                            'name'=>'One Week',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                            ]
                        ], 'jobseeker_one_month'=>[
                            'name'=>'One Month',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                            ]
                        ], 'jobseeker_three_months'=>[
                                'name'=>'Three Month',
                                'features'=>[
                                    'Unlimited',
                                    'Advance',
                                    'Unlimited Incognito'
            
                                ]
                         ], 'jobseeker_six_months'=>[
                                    'name'=>'Six Month',
                                    'features'=>[
                                        'Unlimited',
                                        'Advance',
                                        'Unlimited Incognito'
                
                                    ]
                        ]
                    ],
                    'superChat'=>[
                        "jobseeker_super_chat"=>[
                            'name'=>'Super Chat',
                            "features"=>[
                                "Unfiltered chat messages",
                                "Share social media accounts",
                                "Share email and phone numbers",
                              
                            ]
                       ]],
                    
                ],
                'android'=> [
                    'products'=>[ 
                        'jobseeker_one_week'=>[
                            'name'=>'One Week',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
    
                            ]
                        ], 'jobseeker_one_month'=>[
                            'name'=>'One Month',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
    
                            ]
                        ],   
                            'jobseeker_three_months'=>[
                                'name'=>'Three Months',
                                'features'=>[
                                    'Unlimited',
                                    'Advance',
                                    'Unlimited Incognito'
            
                                ]
                         ], 'jobseeker_six_months'=>[
                                    'name'=>'Six Months',
                                    'features'=>[
                                        'Unlimited',
                                        'Advance',
                                        'Unlimited Incognito'
                
                                    ]
                        ]
                    ],
                     'superChat'=>[
                           'jobseeker_super_chat'=>[
                                    'name'=>'Super Chat',
                                    "features"=>[
                                        "Unfiltered chat messages",
                                        "Share social media accounts",
                                        "Share email and phone numbers"
                                    ]
                                    ],
                                ],
                  
                ]
            ],
            'company'=>[
                'ios'=> [
                    'products'=> [
                        'company_one_week'=>[
                            'name'=>'One Week',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                            ]
                         ], 'company_one_month'=>[
                            'name'=>'One Month',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                            ]
                        ],
                         'company_three_months'=>[
                            'name'=>'Three Months',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                           ]
        
                        ],
                         'company_six_months'=>[
                            'name'=>'Six Months',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
        
                            ]
                         ]
                    ],
                    'superChat'=>[
                        "company_super_chat"=>[
                            'name'=>'Super Chat',
                            "features"=>[
                                "Unfiltered chat messages",
                                "Share social media accounts",
                                "Share email and phone numbers",
                                "admin assist "
                            ]
                        ]
                     ],
                     
                ],
                'android'=> [
                    'products'=> [
                        'company_one_week'=>[
                            'name'=>'One Week',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
            
                            ]
                        ], 'company_one_month'=>[
                            'name'=>'One Month',
                            'features'=>[
                                'Unlimited',
                                'Advance',
                                'Unlimited Incognito'
            
                            ]
                            ],
                            'company_three_months'=>[
                                'name'=>'Three Months',
                                'features'=>[
                                    'Unlimited',
                                    'Advance',
                                    'Unlimited Incognito'
            
                            ]
            
                            ],
                            'company_six_months'=>[
                                'name'=>'Six Months',
                                'features'=>[
                                    'Unlimited',
                                    'Advance',
                                    'Unlimited Incognito'
            
                                ]
                            ]
                    ],
                    'superChat'=>[
                        "company_super_chat"=>[
                            'name'=>'Super Chat',
                            "features"=>[
                                "Unfiltered chat messages",
                                "Share social media accounts",
                                "Share email and phone numbers",
                                "admin assist "
                            ]
                    ]
                    ],
                    ]
            ]
        ],
       'subscription_email_template'=>[

         'subscription_start'=>[
              'heading'=>'Payment Confirmation for JOBMATCHY',
              'title'=>'Thank you for your payment to JOBMATCHY.',
              'description'=>'We have confirmed the payment for the following contract and would like to provide you with the details',
              'name'=>'Name',
              'contract_details'=>'Contract Details',
              'contract_details_text'=>'JOBMATCHY Service Fee',
              'contract_period'=>'Contract Period',
              'payment_method'=>'Payment Method',
              'payment_amount'=>'Payment Amount (10% applicable)',
              'payment_confirmation_date'=>'Payment Confirmation Date',
              'issuer'=>'Issuer of Eligible Invoice',
              'issuer_text'=>'Our Works Co., Ltd',
              'auto_renew'=>'If you are enrolled in automatic renewal, the next service fee will be automatically deducted in the month of contract expiration',
              'auto_renew_stop'=>'If you wish to stop automatic renewal, please proceed through the settings.',
              'pcVersion' =>'PC Version',
              'email_sent'=>'This email is sent from JOBMATCHY',
              'customized_URL'=>'Customized URL',
              'inquiry_form_text'=>'If you have any questions or concerns, please contact us via the inquiry form.',
              'inquiry_form'=>'Inquiry Form'



         ],
        

        ],
        'chat'=>[
            'unrestricted'=>[
                'envelope_title'=>'Unrestricted Chat',
                'page_heading'=>'Unrestricted chat payment',
                'page_title'=>'Thank you for using JOB MATCHY',
                'page_description'=>'To initiate unrestricted chat please make payment using following link or make  transfer bank account ',
                'bank_from_ja'=>'Japanese side account',
                'bank_from_np'=>'Nepal side account',
                'bank_name'=>'Bank Name',
                'bank_name_text'=>'Rakuten Bank',
                'bank_branch'=>'Branch',
                'bank_branch_text'=>'DAI 1, EIGYO SHITEN 251',
                'account_type'=>'Type',
                'account_type_text'=>'Normal',
                'account_number'=>'Account No',
                'account_number_text'=>'7983503',
                'account_name'=>'Account Name',
                'account_name_text' =>'Ｏｕｒ　Ｗｏｒｋｓ　株式会社 
                カナ：アウア ワ－クス（カ',
                'information_text'=>'Incase of bank transfer, please contant us with the information below or mail to'
            ]
          
        ],
       'dear_admin'=>'Dear Admin',
       'best_regards'=>'Best regards',
       'include'=>'included',
       'subscribe'=>'Subscribe',
       'plan'=>'plan',
       'payment_link'=>'Payment Link',
       'contact_form'=>'https://docs.google.com/forms/d/1w6FCDe8Bl1OL1IkTTCeNgEL3AMz__vjBMnb2_lG8Gtk/viewform?edit_requested=true',
       'company_email'=>'info@jobmatchy.net',
       'account_cancellation'=>[
            'title_one'=>'Please check the reasons for cancellation:',
            'dissatisfaction_with_service_quality'=> 'Dissatisfaction with service quality',
            'high_fees'=>'High fees',
            'poor_support'=>'Poor support',
            'poor_email_response'=>'Poor email response',
            'lack_of_contact_from_the_other_party' => 'Lack of contact from the other party',
            'poor_technical_support' => 'Poor technical support',
            'complicated_payment_procedures' => 'Complicated payment procedures',
            'low_frequency_of_use' => 'Low frequency of use',
            'experienced_troubles'=>'Experienced troubles',
            'other'=>'Other',
            'title_two' => 'Would you consider continuing if the fees were lower?:',
            'considering'=> 'Considering',
            'willing_to_consider_depending_on_the_price'=>'Willing to consider depending on the price',
            'not_considering'=>'Not considering',
            'title_three' => 'Please tell us about your future plans:',
            'use_other_service'=>'use other service ',
            'continue_working_in_current_company'=>'continue working in current company ',
            'title_four'=>'Please share any necessary suggestions or comments about this service'
       ],
       'account_delete'=>[
        'title'=>'Your account has been deleted',
        'description'=>'We want to ensure that all aspects of your subscription are properly managed. If you have any active subscriptions with automatic renewal, please take a moment to cancel them to avoid any future charges. You can do this by following these steps:',
        'for_ios' => 'For IOS',
        'for_ios_1'=>'Open the App Store',
        'for_ios_2'=>'Go to Account Settings.',
        'for_ios_3'=>'Select Purchase History',
        'for_ios_4'=>'Cancel your subscription.',
        'for_android'=>'For Android',
        'for_android_1' => 'Open the Play Store',
        'for_android_2' => 'Go to Account Settings',
        'for_android_3' => 'Select Payments and Subscriptions.',
        'for_android_4' => 'Cancel your subscription',
        ]
      
    ];

?>