<?php

namespace Database\Seeders;

use App\Models\Pricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(env('APP_ENV') == 'production' || env('APP_ENV') == 'prod') {
            $plans = [
                'one_week' => [
                    'jobseeker_one_week' => [
                        'android' => 'jobseeker_one_week_np',
                        'ios' => 'jobseeker_one_week_np',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$1.12',
                            'ja' => '',
                            'np' => 'NPR.150',
                        ],
                    ],
                    'company_one_week' => [
                        'android' => 'company_one_week_np',
                        'ios' => 'company_one_week_np',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$5.77',
                            'ja' => '',
                            'np' => 'NPR.770',
                        ],
                    ],
                ],
                'one_month' => [
                    'jobseeker_one_month' => [
                        'android' => 'jobseeker_one_month_np',
                        'ios' => 'jobseeker_one_month_np',
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$3.37',
                            'ja' => '',
                            'np' => 'NPR.450',
                        ],
                    ],
                    'company_one_month' => [
                        'android' => 'company_one_month_np',
                        'ios' => 'company_one_month_np',
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$17.24',
                            'ja' => '',
                            'np' => 'NPR.2300',
                        ],
                    ],
                ],
                'three_months' => [
                    'jobseeker_three_months' => [
                        'android' => 'jobseeker_three_months_np',
                        'ios' => 'jobseeker_three_months_np',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$8.43',
                            'ja' => '',
                            'np' => 'NPR.1125',
                        ],
                    ],
                    'company_three_months' => [
                        'android' => 'company_three_months_np',
                        'ios' => 'company_three_months_np',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$43.28',
                            'ja' => '',
                            'np' => 'NPR.5775',
                        ],
                    ],
                ],
                'six_months' => [
                    'jobseeker_six_months' => [
                        'android' => 'jobseeker_six_months_np',
                        'ios' => 'jobseeker_six_months_np',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$15.18',
                            'ja' => '',
                            'np' => 'NPR.2025',
                        ],
                    ],
                    'company_six_months' => [
                        'android' => 'company_six_months_np',
                        'ios' => 'company_six_months_np',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$77.91',
                            'ja' => '',
                            'np' => 'NPR.10395',
                        ],
                    ],
                ],
                'super_chat' => [
                    'super_chat' => [
                        'android' => 'super_chat',
                        'ios' => 'super_chat',
                        'name' => ['en' => 'Super Chat', 'ja' => 'Super Chat'],
                        'pricing_type' => 'superchat',
                        'user_type' => null,
                        'time_period' => [
                            'en' => '',
                            'ja' => '',
                        ],
                        'features' => [
                            'en' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'ja' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ]
                        ],
                        'prices' => [
                            'en' => '$7.49',
                            'ja' => '',
                            'np' => 'NPR.1000',
                        ],
                    ]
                ]
            ];

        }
        elseif(env('APP_ENV') == 'dev') {
            $plans = [
                'one_week' => [
                    'jobseeker_one_week' => [
                        'android' => 'dev_jobseeker_one_week',
                        'ios' => 'dev_jobseeker_one_week',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$1.12',
                            'ja' => '',
                            'np' => 'NPR.150',
                        ],
                    ],
                    'company_one_week' => [
                        'android' => 'dev_company_one_week',
                        'ios' => 'dev_company_one_week',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$5.77',
                            'ja' => '',
                            'np' => 'NPR.770',
                        ],
                    ],
                ],
                'one_month' => [
                    'jobseeker_one_month' => [
                        'android' => 'dev_jobseeker_one_month',
                        'ios' => 'dev_jobseeker_one_month',
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$3.37',
                            'ja' => '',
                            'np' => 'NPR.450',
                        ],
                    ],
                    'company_one_month' => [
                        'android' => 'dev_company_one_month',
                        'ios' => 'dev_company_one_month',
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$17.24',
                            'ja' => '',
                            'np' => 'NPR.2300',
                        ],
                    ],
                ],
                'three_months' => [
                    'jobseeker_three_months' => [
                        'android' => 'dev_jobseeker_three_months',
                        'ios' => 'dev_jobseeker_three_months',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$8.43',
                            'ja' => '',
                            'np' => 'NPR.1125',
                        ],
                    ],
                    'company_three_months' => [
                        'android' => 'dev_company_three_months',
                        'ios' => 'dev_company_three_months',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$43.28',
                            'ja' => '',
                            'np' => 'NPR.5775',
                        ],
                    ],
                ],
                'six_months' => [
                    'jobseeker_six_months' => [
                        'android' => 'dev_jobseeker_six_months',
                        'ios' => 'dev_jobseeker_six_months',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$15.18',
                            'ja' => '',
                            'np' => 'NPR.2025',
                        ],
                    ],
                    'company_six_months' => [
                        'android' => 'dev_company_six_months',
                        'ios' => 'dev_company_six_months',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$77.91',
                            'ja' => '',
                            'np' => 'NPR.10395',
                        ],
                    ],
                ],
                'super_chat' => [
                    'super_chat' => [
                        'android' => 'super_chat',
                        'ios' => 'super_chat',
                        'name' => ['en' => 'Super Chat', 'ja' => 'Super Chat'],
                        'pricing_type' => 'superchat',
                        'user_type' => null,
                        'time_period' => [
                            'en' => '',
                            'ja' => '',
                        ],
                        'features' => [
                            'en' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'ja' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ]
                        ],
                        'prices' => [
                            'en' => '$7.49',
                            'ja' => '',
                            'np' => 'NPR.1000',
                        ],
                    ]
                ]
            ];

        } else {
            $plans = [
                'one_week' => [
                    'jobseeker_one_week' => [
                        'android' => 'jobseeker_one_week',
                        'ios' => 'jobseeker_one_week_subscription',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$1.12',
                            'ja' => '',
                            'np' => 'NPR.150',
                        ],
                    ],
                    'company_one_week' => [
                        'android' => 'company_one_week',
                        'ios' => 'company_one_week_subscription',
                        'name' => ['en' => 'One Week', 'ja' => '１週間プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => 'week',
                            'ja' => '週',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$5.77',
                            'ja' => '',
                            'np' => 'NPR.770',
                        ],
                    ],
                ],
                'one_month' => [
                    'jobseeker_one_month' => [
                        'android' => 'jobseeker_one_month',
                        'ios' => 'jobseeker_one_month_subscription',
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$3.37',
                            'ja' => '',
                            'np' => 'NPR.450',
                        ],
                    ],
                    'company_one_month' => [
                        'android' => 'company_one_month',
                        'ios' => 'company_one_month_subscription',
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'name' => ['en' => 'One Month', 'ja' => '１ヶ月プラン'],
                        'time_period' => [
                            'en' => 'month',
                            'ja' => '月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$17.24',
                            'ja' => '',
                            'np' => 'NPR.2300',
                        ],
                    ],
                ],
                'three_months' => [
                    'jobseeker_three_months' => [
                        'android' => 'jobseeker_three_months',
                        'ios' => 'jobseeker_three_months_subscription',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$8.43',
                            'ja' => '',
                            'np' => 'NPR.1125',
                        ],
                    ],
                    'company_three_months' => [
                        'android' => 'company_three_months',
                        'ios' => 'company_three_months_subscription',
                        'name' => ['en' => 'Three Months', 'ja' => '3ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '3 months',
                            'ja' => '3ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$43.28',
                            'ja' => '',
                            'np' => 'NPR.5775',
                        ],
                    ],
                ],
                'six_months' => [
                    'jobseeker_six_months' => [
                        'android' => 'jobseeker_six_months',
                        'ios' => 'jobseeker_six_months_subscription',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 1,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ]
                        ],
                        'prices' => [
                            'en' => '$15.18',
                            'ja' => '',
                            'np' => 'NPR.2025',
                        ],
                    ],
                    'company_six_months' => [
                        'android' => 'company_six_months',
                        'ios' => 'company_six_months_subscription',
                        'name' => ['en' => 'Six Months', 'ja' => '6ヶ月プラン'],
                        'pricing_type' => 'subscription',
                        'user_type' => 2,
                        'time_period' => [
                            'en' => '6 months',
                            'ja' => '6ヶ月',
                        ],
                        'features' => [
                            'en' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'ja' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ]
                        ],
                        'prices' => [
                            'en' => '$77.91',
                            'ja' => '',
                            'np' => 'NPR.10395',
                        ],
                    ],
                ],
                'super_chat' => [
                    'super_chat' => [
                        'android' => 'super_chat',
                        'ios' => 'super_chat',
                        'name' => ['en' => 'Super Chat', 'ja' => 'Super Chat'],
                        'pricing_type' => 'superchat',
                        'user_type' => null,
                        'time_period' => [
                            'en' => '',
                            'ja' => '',
                        ],
                        'features' => [
                            'en' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'ja' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ]
                        ],
                        'prices' => [
                            'en' => '$7.49',
                            'ja' => '',
                            'np' => 'NPR.1000',
                        ],
                    ]
                ]
            ];

        }

        foreach ($plans as $time_plans) {
            foreach ($time_plans as $plan_key=>$pricing ) {
                $pricing['key'] = $plan_key;
                Pricing::updateOrCreate(
                    ['key' => $plan_key],
                    $pricing
                );
            }
        }
    }
}