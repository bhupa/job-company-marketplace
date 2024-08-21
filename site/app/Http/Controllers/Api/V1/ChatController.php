<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Chat\ChatSeenRequest;
use App\Http\Requests\V1\Chat\ChatStoreRequest;
use App\Http\Requests\V1\Chat\DirectChatRequest;
use App\Http\Resources\V1\Chat\ChatDetailsResource;
use App\Http\Resources\V1\Chat\ChatRoomPaginationResource;
use App\Mail\ChatAdminHelpEmail;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Services\ChatRoomService;
use App\Services\ChatService;
use App\Services\FireBaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ChatController extends BaseController
{
    protected $chat;
    protected $room;
    protected $firebase;

    public function __construct(
        ChatService $chat,
        ChatRoomService $room,
        FireBaseService $firebase
    ) {
        $this->chat = $chat;
        $this->room = $room;
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $chats = $this->chat->getMessage($request);

        return $this->success(
            new ChatRoomPaginationResource($chats),
            'Chat user lists'
        );
    }

    public function store(ChatStoreRequest $request)
    {
        try {
            $room = ChatRoom::find($request->chat_room_id);
            $receiver = chatroomReceiveBy($room);
            $roomNoMessage = chatroomNoMessage($room);

            if ($receiver !== null || $roomNoMessage !== null) {
                $chat = $this->chat->create($request);

                return $this->success(
                    new ChatDetailsResource($chat),
                    'Chat create successfully'
                );
            }

            return $this->success([], 'User has deleted their account.');
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function seen(ChatSeenRequest $request, Chat $chat)
    {
        try {
            // code...
            if (empty($chat->seen)) {
                $chat->update(['seen' => $request->seen]);

                return $this->success($chat->refresh(), 'Message seen');
            }
            totalbadgeCount(auth()->user());

            return $this->success($chat, 'Message already seen');
        } catch (\Throwable $th) {
            return $this->errors(
                ['message' => $th->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function sendEmail(ChatRoom $chatroom)
    {
        try {
            $adminEmail = env('ADMIN_EMAIL');
            if ($chatroom->match) {
                $company = $chatroom->match->company;
                $jobseeker = $chatroom->match->jobseeker;
                Mail::to($adminEmail)->send(
                    new ChatAdminHelpEmail($chatroom, $company, $jobseeker)
                );
                $chatroom->update(['admin_assist' => 1]);

                return $this->success([], 'Assist email has been send to admin');
            }

            return $this->errors(
                ['message' => 'Chatroom dont have match', 'type' => 'default'],
                400
            );
        } catch (\Throwable $th) {
            return $this->errors(
                ['message' => $th->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function directChat(DirectChatRequest $request)
    {
        try {
            $subscription = auth()->user()->subscribed_type;

            $totalChat = $this->chat->directChatCount();
            if (is_null($subscription)) {
                $total = env('APP_ENV') == 'local' ? 1 : 1;
                if ($totalChat >= $total) {
                    return $this->errors(
                        [
                            'message' => trans(
                                'lang.chat.limit_month',
                                [],
                                getUserLanguage(auth()->user())
                            ),
                            'type' => 'limit',
                        ],
                        400
                    );
                }
            }
            $room = $this->room->create($request, 'direct', null, 1);
            $request['chat_room_id'] = $room->id;
            $chat = $this->chat->create($request);

            $user = $this->chat->receiveUser($chat);
            if ($user->device_token) {
                $chat['user'] = $user;
                $this->firebase->sendNotification($chat, 'chat');
            }

            $data = [
                'chatRequest' => $this->chat->directChatCount(),
                'items' => new ChatDetailsResource($chat),
            ];

            return $this->success($data, 'Chat create successfully');
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function unseenCount()
    {
        try {
            $result = [
                'receiveBy' => auth()->id(),
                'unseenCount' => unseenCount(auth()->user()),
                'badgeCount' => badgeCount(auth()->user()),
            ];
            violationCountSeen(auth()->user());

            return $this->success([
                'totalChatCount' => $result,
                'Total chat unseen count',
            ]);
        } catch (\Throwable $th) {
            return $this->errors(['message' => $th->getMessage()], 400);
        }
    }
}
