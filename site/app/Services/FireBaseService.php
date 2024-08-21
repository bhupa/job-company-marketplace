<?php
namespace App\Services;

use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Matching;
use App\Notifications\V1\Account\AccountNotification;
use App\Notifications\V1\Account\ViolationNotification;
use App\Notifications\V1\Chat\ChatNotification;
use App\Notifications\V1\Match\ChatRequestNotification;
use App\Notifications\V1\Match\MatchedNotification;
use App\Traits\FirebaseTrait;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class FireBaseService extends BaseService
    {
    use FirebaseTrait;
    protected $chat;

    public function __construct(Matching $matching)
        {
        $this->model = $matching;
        }

    public function sendNotification($matching, $type)
        {
        if ($type == 'chat') {
            $output = $this->sendingChatNotification($matching);
            $user = $output['user'];
            } else {
            $output = $this->sendingAcceptNotification($matching, $type);
            $user =
                $type == 'chat-request-matched'
                ? auth()->user()
                : $matching->createdBy;
            }

        $client = new Client();
        $access_token = $this->getAccessToken();

        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';

        $user->company_name;

        $notificationData = $output['data'];
        $notificationData['body'] = [
            'title' => $output['data']['title'],
            'data' => [
                'url' => $output['url'],
            ],
        ];

        $notificationId = $this->notification(
            $user,
            $type,
            'App\Model\Matching',
            $notificationData,
            auth()->id()
        );

        $message = [
            'message' => [
                'token' => $user->device_token, // Use 'token' field for sending to a single device
                'notification' => $output['data'],
                'data' => [
                    'notificationId' => $notificationId,
                    'route' => $output['url'],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'badge' => $output['badge'],
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
        ];

        $output = $client->post($fcmUrl, [
            'headers' => $headers,
            'json' => $message,
        ]);

        $respose = json_decode($output->getStatusCode());

        return json_decode($output->getBody());
        }

    public function sendOtp($user, $type, $output)
        {
        $client = new Client();
        $fcmUrl = 'https://fcm.googleapis.com/v1/projects/' . env('FIREBASE_PROJECT_ID') . '/messages:send';

        $access_token = $this->getAccessToken();
        $route = null;
        if ($type == 'user-account-verify') {
            $data = [
                'title' => trans(
                    'lang.notification.account-verification.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => trans(
                    'lang.notification.account-verification.body',
                    [],
                    getUserLanguage($user)
                ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://profile/accountVerification';
            $this->notification($user, 'account', $output, auth()->id());
            } elseif ($type == 'user-account-rejected') {
            $data = [
                'title' => trans(
                    'lang.notification.account-rejected.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => trans(
                    'lang.notification.account-rejected.body',
                    [],
                    getUserLanguage($user)
                ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://profile/accountVerification';
            $this->notification($user, 'account', $output, auth()->id());
            } elseif ($type == 'chat-request') {
            $name = auth()->user()->fullName;
            $data = [
                'title' => trans(
                    'lang.notification.chat-request.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => $name .
                    ' ' .
                    trans(
                        'lang.notification.chat-request.body',
                        [],
                        getUserLanguage($user)
                    ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://chat/' . $user->roomId;
            $this->notification($user, 'chat-request', $output, auth()->id());
            } elseif ($type == 'violation-approved') {
            $data = [
                'title' => trans(
                    'lang.notification.violation-approve.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => trans(
                    'lang.notification.violation-approve.body',
                    [],
                    getUserLanguage($user)
                ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://profile';
            $this->notification($user, 'violation', $output, auth()->id());
            } elseif ($type == 'violation-reject') {
            $data = [
                'title' => trans(
                    'lang.notification.violation-rejected.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => trans(
                    'lang.notification.violation-rejected.body',
                    [],
                    getUserLanguage($user)
                ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://profile';
            $this->notification($user, 'violation', $output, auth()->id());
            } elseif ($type == 'unrestricted-chat') {
            $data = [
                'title' => trans(
                    'lang.notification.unrestricted-chat.title',
                    [],
                    getUserLanguage($user)
                ),
                'body' => trans(
                    'lang.notification.unrestricted-chat.body',
                    [],
                    getUserLanguage($user)
                ),
            ];
            $badge = badgeCount($user);
            $route = env('NOTIFICATION_URL') . '://chat/' . $output->id;
            $this->notification($user, 'chat', $output, auth()->id());
            } else {
            $data = [
                'title' => 'Your OTP for password reset',
                'body' => $user->otp,
                $badge = null,
            ];
            }
        Log::info('user device token ');
        Log::info($user->device_token);
        $message = [
            'message' => [
                'token' => $user->device_token, // Use 'token' field for sending to a single device
                'notification' => $data,
                'data' => [
                    'route' => $route,
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'badge' => $badge,
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        Log::info('access token');
        Log::info($access_token);

        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
        ];

        $output = $client->post($fcmUrl, [
            'headers' => $headers,
            'json' => $message,
        ]);

        $respose = json_decode($output->getStatusCode());
        Log::info('json decode ');
        Log::info($respose);

        return true;
        }

    public function sendingAcceptNotification($matching, $type)
        {
        $user =
            $type == 'chat-request-matched'
            ? auth()->user()
            : $matching->createdBy;

        $this->notification($user, 'matched', $matching, auth()->id());
        $room = ChatRoom::where('matching_id', $matching->id)->first();
        $name = ($matching->created_by != $matching->jobseeker->user_id) ? $matching->jobseeker->user->fullName :
            $matching->company->user->fullName;
        $output['data'] = [
            'title' => trans(
                'lang.notification.chat-request-matched.title',
                [],
                getUserLanguage($user)
            ),
            'body' => $name . ' ' . trans(
                'lang.notification.chat-request-matched.body',
                [],
                getUserLanguage($user)
            ),

        ];

        $output['url'] = env('NOTIFICATION_URL') . '://chat/' . $room->id;
        $output['badge'] = badgeCount($user);

        return $output;
        }

    public function sendingChatNotification($chatDetail)
        {
        $chat = Chat::find($chatDetail->id);

        $name =
            $chat->sendBy->user_type == 1
            ? $chat->sendBy->jobseeker->first_name .
            ' ' .
            $chat->sendBy->jobseeker->last_name
            : $chat->sendBy->company->company_name;

        $output['user'] = $chatDetail->user;
        $room = $chat->room;
        $user =
            $chat->send_by != $room->created_by
            ? $room->createdBy
            : ($room->match->jobseeker->user_id == $chat->send_by
                ? $room->match->company->user
                : $room->match->jobseeker->user);
        $output['user'] = $user;
        $this->notification($user, 'chat', $chatDetail, auth()->id());
        $output['data'] = [
            'title' => trans(
                'lang.notification.chat.title',
                [],
                getUserLanguage($user)
            ),
            'body' => $name .
                ' ' .
                trans(
                    'lang.notification.chat.body',
                    [],
                    getUserLanguage($user)
                ),
        ];

        $output['url'] = env('NOTIFICATION_URL') . '://chat/' . $chat->chat_room_id;
        $output['room'] = $chatDetail->chat_room_id;
        $output['badge'] = badgeCount($user);

        return $output;
        }

    public function notification($user, $type, $notificationData, $createdBy)
        {
        $notificationId = Uuid::uuid4()->toString();
        if ($type === 'matched') {
            $user->notify(
                new MatchedNotification(
                    $notificationId,
                    $createdBy,
                    $notificationData
                )
            );
            } elseif ($type === 'chat') {
            $user->notify(
                new ChatNotification(
                    $notificationId,
                    $createdBy,
                    $notificationData
                )
            );
            } elseif ($type === 'account') {
            $user->notify(
                new AccountNotification(
                    $notificationId,
                    $createdBy,
                    $notificationData
                )
            );
            } elseif ($type === 'chat-request') {
            $user->notify(
                new ChatRequestNotification(
                    $notificationId,
                    $createdBy,
                    $notificationData
                )
            );
            } elseif ($type === 'violation') {
            $user->notify(
                new ViolationNotification(
                    $notificationId,
                    $createdBy,
                    $notificationData
                )
            );
            }

        return $notificationId;
        }
    }