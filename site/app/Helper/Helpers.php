<?php

use App\Events\V1\ChatUnseenCountEvent;
use App\Events\V1\SubscriptionActionEvent;
use App\Http\Resources\V1\InappPurchase\InappPurchaseResourceDetails;
use App\Http\Resources\V1\Subscription\SubscriptionDetailsResource;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\V1\District;
use App\Models\V1\InAppPurchase;
use App\Models\V1\Prefecture;
use App\Notifications\V1\Account\AccountNotification;
use App\Notifications\V1\Account\ViolationNotification;
use App\Notifications\V1\Chat\UnrestrictedChatNotification;
use App\Notifications\V1\Match\MatchedNotification;
use App\Services\FireBaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Arr;
use Stripe\Price;
use Illuminate\Support\Str;
use Stripe\Product;
use Stripe\Stripe;

if (!function_exists('getInAppPlan')) {
    function getInAppPlan($output,$user)
    {
       
        //chekcing the server
        if (env('APP_ENV') == 'production') {
            $lists = iapListProd($user);
        } elseif (env('APP_ENV') == 'staging') {
            $lists = iapListStagging($user);
        } else {
            $lists = iapLists($user);
        }
        $chat = Str::contains($output->item_id, 'super_chat');
        if ($chat) {
            if ($output->payment_type == 'apple') {
                $plan = $lists['ios']['superChat'][$output->item_id];
            } else {
                $plan = $lists['android']['superChat'][$output->item_id];
            }
        } else {
            if (
                ($output->payment_type == 'apple' && $chat == false) ||
                $output->payment_type == 'trial'
            ) {
                $plan = $lists['ios']['subscription'][$output->item_id];
            } else {
                $plan = $lists['android']['subscription'][$output->item_id];
            }
        }

        return $plan;
    }
}
if (!function_exists('subscriptionAction')) {
    function subscriptionAction($userId)
    {
        $data = [
            'userId' => $userId,
        ];
        return broadcast(new SubscriptionActionEvent($data));
    }
}
if (!function_exists('getSubscribedPlan')) {
    function getSubscribedPlan()
    {
        $data = null;
        $subscription = auth()->user()->subscribed_type;
        Stripe::setApiKey(env('STRIPE_SECRET'));
        if (
            auth()->user()->subscriptions_type == 'iap' ||
            auth()->user()->subscriptions_type == 'trial' ||
            (auth()->user()->subscriptions_type == 'admin-pay' &&
                auth()->user()->SubscribedType) 
        ) {
            $inapp = auth()->user()->SubscribedType;

            $data = $inapp
                ? [
                    'isSubscribed' =>
                        $inapp->status == 'expired' ? false : true,
                    'subscription' => new InappPurchaseResourceDetails($inapp),
                ]
                : ['isSubscribed' => false, 'subscription' => null];
        } elseif (
            auth()->user()->subscriptions_type == 'stripe' &&
            $subscription
        ) {
           
            $data = [
                'isSubscribed' => true,
                'subscription' => new SubscriptionDetailsResource(
                    $subscription
                ),
            ];
        }
        return $data;
    }
}
if (!function_exists('subscriptionAction')) {
    function subscriptionAction($userId)
    {
        $data = [
            'userId' => $userId,
            // 'subscriptionDetails'=>new InappPurchaseResourceDetails( $output)
        ];
        return broadcast(new SubscriptionActionEvent($data));
    }
}

if (!function_exists('checkTrueFalse')) {
    function checkTrueFalse($value)
    {
        if ($value == 1) {
            return 'Yes';
        }
        return 'No';
    }
}
if (!function_exists('resetRedisData')) {
    function resetRedisData($type, $userId)
    {
        $key =
            $type == 'jobs'
                ? 'jobs_' . auth()->id()
                : 'jobseeker_' . auth()->id();
        $value = Redis::exists($key) ? Redis::get($key) : null;
        $value && Redis::del($key, $value);
        return true;
    }
}
if (!function_exists('getTrialSubscription')) {
    function getTrialSubscription($user)
    {
        $server = env('APP_ENV') == 'dev' ? 'dev' : 'staging';
        if (env('APP_ENV') == 'production') {
            $item =
                $user->user_type == 1
                    ? 'jobseeker_one_month'
                    : 'company_one_month';
        } elseif (env('APP_ENV') == 'staging') {
            $item =
                $user->user_type == 1
                    ? 'jobseeker_one_month_subscription'
                    : 'company_one_month_subscription';
        } else {
            $item =
                $user->user_type == 1
                    ? 'dev_jobseeker_one_month_subscription'
                    : 'dev_company_one_month_subscription';
        }

        $currentDate = Carbon::now();
        $ends_at = $currentDate->addMonths(1);
        $subscription = [
            'user_id' => $user->id,
            'item_id' => $item,
            'payment_type' => 'trial',
            'payment_for' => 'subscription',
            'status' => 'trial',
            'ends_at' => $ends_at ? $ends_at->toDateTimeString() : null,
        ];

        $user->update(['subscriptions_type' => 'trial']);
        return InAppPurchase::create($subscription);
    }
}

if (!function_exists('badgeCount')) {
    function badgeCount($user)
    {
        $chat = unseenCount($user);
        $match = $user
            ->unreadNotifications()
            ->where('type', MatchedNotification::class)
            ->whereNull('read_at')
            ->get();
        $account = $user
            ->unreadNotifications()
            ->where('type', AccountNotification::class)
            ->whereNull('read_at')
            ->get();
        $violation = $user
            ->unreadNotifications()
            ->where('type', ViolationNotification::class)
            ->whereNull('read_at')
            ->get();
        $unRestrictedChat = $user
            ->unreadNotifications()
            ->where('type', UnrestrictedChatNotification::class)
            ->whereNull('read_at')
            ->get();
        $total =
            $chat +
            count($match) +
            count($account) +
            count($violation) +
            count($unRestrictedChat);
        return $total;
    }
}

if (!function_exists('getStripePlan')) {
    function getStripePlan($product)
    {
        $data['id'] = $product->id;
        $data['name'] = [
            'en' => $product->metadata['name_en'],
            'ja' => $product->metadata['name_ja'],
        ];
        $data['timePeriod'] = [
            'en' => $product->metadata['duration_en'],
            'ja' => $product->metadata['duration_ja'],
        ];
        // $data['duration'] =$product->metadata['duration'];
        if ($product->name != 'Superchat') {
            // Retrieve the price for this product
            if ($product->metadata['description'] == 'week') {
                $data['order'] = 1;
            }
            if ($product->metadata['description'] == 'two week') {
                $data['order'] = 2;
            }
            if ($product->metadata['description'] == 'month') {
                $data['order'] = 3;
            }
            if ($product->metadata['description'] == 'three months') {
                $data['order'] = 4;
            }
            if ($product->metadata['description'] == 'six months') {
                $data['order'] = 5;
            }
            $price = Price::all(['product' => $product->id]);
          

            $features = $product->features;
            $items = explode(',', $product->metadata['features_ja']);
            $result = [];
            foreach ($items as $item) {
                $item = trim($item);
                $result[] = $item;
            }

            // Initialize an empty array to store the result
            $result = [];

            // Loop through each item and extract the value of the "name" key
            foreach ($features as $item) {
                $result[] = $item['name'];
            }
            $data['features'] = [
                'en' => $result,
                'ja' => $items,
            ];
          
            // Add the price tos the product object
            $product->price = $price;
            foreach ($product->price['data'] as $pdPrice) {

                if($pdPrice->lookup_key != ''){
                    if ($pdPrice->currency == 'jpy') {
                        $data['price']['ja'] = [
                            'id' => $pdPrice->id,
                            'price' => (string) $pdPrice->unit_amount,
                            'currency' => $pdPrice->currency,
                            'symbol' => '¥',
                            'lookup' => $pdPrice->lookup_key,
                        ];
                        } elseif ($pdPrice->currency == 'usd') {
                        $data['price']['en'] = [
                            'id' => $pdPrice->id,
                            'price' => (string) ($pdPrice->unit_amount / 100),
                            'symbol' => '$',
                            'currency' => $pdPrice->currency,
                            'lookup' => $pdPrice->lookup_key,
                        ];
                        } else {
                        $data['price']['npr'] = [
                            'id' => $pdPrice->id,
                            'price' => (string) ($pdPrice->unit_amount / 100),
                            'symbol' => 'रु',
                            'currency' => $pdPrice->currency,
                            'lookup' => $pdPrice->lookup_key,
                        ];
                        }

                }
                
            }
        
            return $data;
        }
        return null;
    }
}
if (!function_exists('violationCountSeen')) {
    function violationCountSeen($user)
    {
        $violation = $user
            ->unreadNotifications()
            ->where('type', ViolationNotification::class)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()->utc()]);
        return totalbadgeCount($user);
    }
}

if (!function_exists('totalbadgeCount')) {
    function totalbadgeCount($user)
    {
        $totalUnseen = unseenCount($user);
        $unseen = [
            'receiveBy' => $user->id,
            'unseenCount' => $totalUnseen,
            'badgeCount' => badgeCount($user),
        ];

        broadcast(new ChatUnseenCountEvent($unseen));

        return true;
    }
}
if (!function_exists('setLeftSwipeJobseekers')) {
    function setLeftSwipeJobseekers($jobseekers)
    {
        $userId = auth()->id();
        $company = 'jobseeker_' . $userId;

        // Retrieve the existing data from Redis
        $existingData = Redis::get($company);

        // Decode the existing JSON data if it exists
        $existingJobseekers = $existingData
            ? json_decode($existingData, true)
            : [];

        // Iterate through each jobseeker in the $jobseekers array
        foreach ($jobseekers as $jobseeker) {
            // Check if the current jobseeker exists in the $existingJobseekers array
            if (!in_array($jobseeker, $existingJobseekers)) {
                // If the jobseeker is not found in the existing list, add it to the list
                $existingJobseekers[] = $jobseeker;
            }
        }

        // Encode the updated data as JSON
        $jsonData = json_encode($existingJobseekers);

        // Set the updated data in Redis
        Redis::set($company, $jsonData);
        // Return the updated data
        return $existingJobseekers;
    }
}
if (!function_exists('getLeftSwipeJobseekers')) {
    function getLeftSwipeJobseekers()
    {
        $company = 'jobseeker_' . auth()->id();
        $output = Redis::get($company);
        return json_decode($output, true);
    }
}
if (!function_exists('removeFromLeftSwipeJobseekers')) {
    function removeFromLeftSwipeJobseekers($valueToRemove)
        {
        $userId = auth()->id();
        $company = 'jobseeker_' . $userId;
        // Retrieve the existing data from Redis
        $existingData = Redis::get($company);

        // Decode the existing JSON data if it exists
        $existingJobseekers = $existingData
            ? json_decode($existingData, true)
            : [];

        // Check if the value to remove exists in the list
        $index = array_search($valueToRemove, $existingJobseekers);
        if ($index !== false) {
            // If the value is found, remove it from the list
            unset($existingJobseekers[$index]);
            }

        // Encode the updated data as JSON
        $jsonData = json_encode(array_values($existingJobseekers)); // Reindex the array after removal

        // Set the updated data in Redis
        Redis::set($company, $jsonData);

        // Return the updated data
        return $existingJobseekers;
        }
    }
if (!function_exists('setLeftSwipeJobs')) {
    function setLeftSwipeJobs($jobs)
    {
        $userId = auth()->id();
        $jobseeker = 'jobs_' . $userId;
        // Retrieve the existing data from Redis
        $existingData = Redis::get($jobseeker);
        // Decode the existing JSON data if it exists
        $existingJobs = $existingData ? json_decode($existingData, true) : [];
        // Check if each job already exists in the existing data
        foreach ($jobs as $job) {
            if (!in_array($job, $existingJobs)) {
                // Add the job to the existing data if it doesn't already exist
                $existingJobs[] = $job;
            }
        }
        // Encode the updated data as JSON
        $jsonData = json_encode($existingJobs);
        // Set the updated data in Redis
        Redis::set($jobseeker, $jsonData);
        // Return the updated data
        return $existingJobs;
    }
}

if (!function_exists('removeLeftSwipeJobs')) {
    function removeLeftSwipeJobs($jobsToRemove)
    {
        $userId = auth()->id();
        $jobseeker = 'jobs_' . $userId;
        
        $existingData = Redis::get($jobseeker);
        // Decode the existing JSON data if it exists
        $existingJobs = $existingData ? json_decode($existingData, true) : [];
        // Remove the specified jobs from the existing data
        $index = array_search($jobsToRemove, $existingJobs);
        if ($index !== false) {
            // If the value is found, remove it from the list
            unset($existingJobs[$index]);
            }

        // Encode the updated data as JSON
        $jsonData = json_encode(array_values($existingJobs)); // Reindex the array after removal

        // Set the updated data in Redis
        Redis::set($jobseeker, $jsonData);
        
        // Return the updated data
        return $existingJobs;
    }
}
if (!function_exists('getLeftSwipeJobs')) {
    function getLeftSwipeJobs()
    {
        $jobs = 'jobs_' . auth()->id();
        $output = Redis::get($jobs);
        return json_decode($output, true);
    }
}

if (!function_exists('setUserLanguage')) {
    function setUserLanguage($lang)
    {
        $userLang = 'user_lang_'.auth()->id();
        Redis::set($userLang, $lang);
        return Redis::exists($userLang);
    }
}
if (!function_exists('getUserLanguage')) {
    function getUserLanguage($user)
    {
        $userLang = 'user_lang_'.$user->id;
        return Redis::exists($userLang) ? Redis::get($userLang) : 'en';
    }
}

if (!function_exists('iapLists')) {
    function iapLists($user)
    {
        if ($user->user_type == 1) {
            // products replace by  subscription for prod
            $data = [
                'ios' => [
                    'subscription' => [
                        'dev_jobseeker_oneweek_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$2.99',
                                'ja' => '¥330',
                                'npr' => 'NPR.300',
                            ],
                        ],
                        'dev_jobseeker_onemonth_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$6.99',
                                'ja' => '¥990',
                                'npr' => 'NPR.800',
                            ],
                        ],
                        'dev_jobseeker_threemonths_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$16.99',
                                'ja' => '¥2,480',
                                'npr' => 'NPR.2,000',
                            ],
                        ],
                        'dev_jobseeker_sixmonths_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$29.99',
                                'ja' => '¥4,480',
                                'npr' => 'NPR.3,500',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'jobseeker_one_week_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$2.99',
                                'ja' => '¥330',
                                'npr' => 'NPR.300',
                            ],
                        ],
                        'jobseeker_one_month_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$6.99',
                                'ja' => '¥990',
                                'npr' => 'NPR.800',
                            ],
                        ],
                        'jobseeker_three_months_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$16.99',
                                'ja' => '¥2,480',
                                'npr' => 'NPR.2,000',
                            ],
                        ],
                        'jobseeker_six_months_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$29.99',
                                'ja' => '¥4,480',
                                'npr' => 'NPR.3,500',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            
                        ],
                    ],
                ],
            ];
        } else {
            $data = [
                'ios' => [
                    'subscription' => [
                        'dev_company_oneweek_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'dev_company_onemonth_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'dev_company_threemonths_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$159.99',
                                'ja' => '¥24,800',
                                'npr' => 'NPR.217999.85',
                            ],
                        ],
                        'dev_company_sixmonths_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥44,800',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'company_one_week_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'company_one_month_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'company_three_months_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$159.99',
                                'ja' => '¥24800',
                                'npr' => 'NPR.217999.85',
                            ],
                        ],
                        'company_six_months_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥44,800',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                           
                        ],
                    ],
                ],
            ];
        }
        return $data;
    }
}
if (!function_exists('iapListStagging')) {
    function iapListStagging($user)
        {
        if ($user->user_type == 1) {
            // products replace by    subscription
            $data = [
                'ios' => [
                    'subscription' => [
                        'jobseeker_oneweek_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$2.99',
                                'ja' => '¥330',
                                'npr' => 'NPR.300',
                            ],
                        ],
                        'jobseeker_onemonth_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$6.99',
                                'ja' => '¥990',
                                'npr' => 'NPR.800',
                            ],
                        ],
                        'jobseeker_threemonths_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$16.99',
                                'ja' => '¥2,480',
                                'npr' => 'NPR.2,000',
                            ],
                        ],
                        'jobseeker_sixmonths_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$29.99',
                                'ja' => '¥4480',
                                'npr' => 'NPR.3500.09',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat_staging' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                           
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'jobseeker_one_week_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3330',
                                'npr' => 'NPR.282',
                            ],
                        ],
                        'jobseeker_one_month_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9990',
                                'npr' => 'NPR.845',
                            ],
                        ],
                        'jobseeker_three_months_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$159.99.24',
                                'ja' => '¥24800',
                                'npr' => 'NPR.2,111',
                            ],
                        ],
                        'jobseeker_six_months_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥44800',
                                'npr' => 'NPR.3800',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat_staging' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                           
                        ],
                    ],
                ],
            ];
            } else {
            $data = [
                'ios' => [
                    'subscription' => [
                        'company_oneweek_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'company_onemonth_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'company_threemonths_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$159.99',
                                'ja' => '¥24800',
                                'npr' => 'NPR.159.85',
                            ],
                        ],
                        'company_sixmonths_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥44,800',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat_staging' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'company_one_week_subscription' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'company_one_month_subscription' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'company_three_months_subscription' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$159.99',
                                'ja' => '¥24800',
                                'npr' => 'NPR.217999.85',
                            ],
                        ],
                        'company_six_months_subscription' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥44,800',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                        ],
                    ],
                ],
            ];
            }
        return $data;
        }
    }

if (!function_exists('iapListProd')) {
    function iapListProd($user)
    {
        if ($user->user_type == 1) {
            // products replace by    subscription
            $data = [
                'ios' => [
                    'subscription' => [
                        'jobseeker_one_week' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$2.99',
                                'ja' => '¥330',
                                'npr' => 'NPR.300',
                            ],
                        ],
                        'jobseeker_one_month' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$6.99',
                                'ja' => '¥990',
                                'npr' => 'NPR.800',
                            ],
                        ],
                        'jobseeker_three_months' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$16.99',
                                'ja' => '¥2,480',
                                'npr' => 'NPR.2,000',
                            ],
                        ],
                        'jobseeker_six_months' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$29.99',
                                'ja' => '¥4,480',
                                'npr' => 'NPR.3,500',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'jobseeker_one_week' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$21.99',
                                'ja' => '¥3330',
                                'npr' => 'NPR.282',
                            ],
                        ],
                        'jobseeker_one_month' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$63.99',
                                'ja' => '¥9990',
                                'npr' => 'NPR.845',
                            ],
                        ],
                        'jobseeker_three_months' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$159.99',
                                'ja' => '¥2,4800',
                                'npr' => 'NPR.2,111',
                            ],
                        ],
                        'jobseeker_six_months' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$290.99',
                                'ja' => '¥4,4800',
                                'npr' => 'NPR.3,800',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'jobseeker_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $data = [
                'ios' => [
                    'subscription' => [
                        'company_one_week' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$21.79',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'company_one_month' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$65.37',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'company_three_months' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$163.44',
                                'ja' => '¥247,500',
                                'npr' => 'NPR.217999.85',
                            ],
                        ],
                        'company_six_months' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$294.19',
                                'ja' => '¥44,550',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                        ],
                    ],
                ],
                'android' => [
                    'subscription' => [
                        'company_one_week' => [
                            'name' => 'One Week',
                            'nameJa' => '１週間プラン',
                            'timePeriod' => [
                                'en' => 'week',
                                'ja' => '週',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'Rewind',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                'プレバック',
                            ],
                            'price' => [
                                'en' => '$21.79',
                                'ja' => '¥3,300',
                                'npr' => 'NPR.2906.66',
                            ],
                        ],
                        'company_one_month' => [
                            'name' => 'One Month',
                            'nameJa' => '１ヶ月プラン',
                            'timePeriod' => [
                                'en' => 'month',
                                'ja' => '月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$65.37',
                                'ja' => '¥9,900',
                                'npr' => 'NPR.8719.99',
                            ],
                        ],
                        'company_three_months' => [
                            'name' => 'Three Months',
                            'nameJa' => '3ヶ月プラン',
                            'timePeriod' => [
                                'en' => '3 months',
                                'ja' => '3ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$163.44',
                                'ja' => '¥247,500',
                                'npr' => 'NPR.217999.85',
                            ],
                        ],
                        'company_six_months' => [
                            'name' => 'Six Months',
                            'nameJa' => '6ヶ月プラン',
                            'timePeriod' => [
                                'en' => '6 months',
                                'ja' => '6ヶ月',
                            ],
                            'features' => [
                                'Unlimited swipe',
                                'Unlimited bookmark',
                                'Unlimited chat request',
                                'More than two posts',
                            ],
                            'featuresJa' => [
                                'Swipe無制限',
                                'Bookmark無制限',
                                'チャットリクエスト無制限',
                                '2求人以上',
                            ],
                            'price' => [
                                'en' => '$294.19',
                                'ja' => '¥44,550',
                                'npr' => 'NPR.39239.97',
                            ],
                        ],
                    ],
                    'superChat' => [
                        'company_super_chat' => [
                            'name' => 'Super Chat',
                            'nameJa' => 'Super Chat',
                            'timePeriod' => [
                                'en' => '',
                                'ja' => '',
                            ],
                            'features' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                            'featuresJa' => [
                                'Unfiltered chat messages',
                                'Share social media accounts',
                                'Share email and phone numbers',
                                'Admin assist',
                            ],
                        ],
                    ],
                ],
            ];
        }
        return $data;
    }
}

if (!function_exists('getLocation')) {
    function getLocation($key)
    {
        $location = [
            1 => 'Hokkaido',
            2 => 'Aomori',
            3 => 'Iwate',
            4 => 'Miyagi',
            5 => 'Akita',
            6 => 'Yamagata',
            7 => 'Fukushima',
            8 => 'Ibaraki',
            9 => 'Tochigi',
            10 => 'Gunma',
            11 => 'Saitama',
            12 => 'Chiba',
            13 => 'Tokyo',
            14 => 'Kanagawa',
            15 => 'Niigata',
            16 => 'Toyama',
            17 => 'Ishikawa',
            18 => 'Fukui',
            19 => 'Yamanashi',
            20 => 'Nagano',
            21 => 'Gifu',
            22 => 'Shizuoka',
            23 => 'Aichi',
            24 => 'Mie',
            25 => 'Shiga',
            26 => 'Kyōto',
            27 => 'Ōsaka',
            28 => 'Hyōgo',
            29 => 'Nara',
            30 => 'Wakayama',
            31 => 'Tottori',
            32 => 'Shimane',
            33 => 'Okayama',
            34 => 'Hiroshima',
            35 => 'Yamaguchi',
            36 => 'Tokushima',
            37 => 'Kagawa',
            38 => 'Ehime',
            39 => 'Kochi',
            40 => 'Fukuoka',
            41 => 'Saga',
            42 => 'Nagasaki',
            43 => 'Kumamoto',
            44 => 'Oita',
            45 => 'Miyazaki',
            46 => 'Kagoshima',
            47 => 'Okinawa',
        ];

        return $location[$key];
    }
}

if (!function_exists('getDistricts')) {
    function getDistricts($key)
    {
        $data = [
            'hokkaido' => [1],
            'tohoku' => [2, 3, 4, 5, 6, 7],
            'kanto' => [8, 9, 10, 11, 12, 13],
            'chubu' => [14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
            'kansai' => [24, 25, 26, 27, 28, 29, 30],
            'chugoku' => [31, 32, 33, 34, 35],
            'Shikoku' => [36, 37, 38, 39],
            'Kyushu' => [40, 41, 42, 43, 44, 45, 46, 47],
        ];

        $result = Arr::has($data, $key) == true ? $data[$key] : null;

        return $result;
    }
}

if (!function_exists('getPrefectureDistricts')) {
    function getPrefectureDistricts($prefectureId)
    {
        $district = District::find($prefectureId);
        return [$district->id];
    }
}

if (!function_exists('getCancellationReasonTitle')) {
    function getCancellationReasonTitle($details, $reasonId)
    {
        $reason = collect($details[0]['options']);
        $result = $reason->first(function ($item) use ($reasonId) {
            return $item['id'] == $reasonId;
        });
        return $result['title'];
    }
}
if (!function_exists('getCancellationSubReasonTitle')) {
    function getCancellationSubReasonTitle($details, $subReasonID)
    {
        $subreason = collect(
            $details[0]['options'][1]['subMenu'][0]['options']
        );
        $subresult = $subreason->first(function ($item) use ($subReasonID) {
            return $item['id'] == $subReasonID;
        });
        return $subresult['title'];
    }
}
if (!function_exists('getCancellationFuturePlanReasonTitle')) {
    function getCancellationFuturePlanReasonTitle($details, $futureID)
    {
        $future = collect($details[1]['options']);
        $futureresult = $future->first(function ($item) use ($futureID) {
            return $item['id'] == $futureID;
        });
        return $futureresult['title'];
    }
}

?>
