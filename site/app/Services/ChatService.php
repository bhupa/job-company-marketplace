<?php

namespace App\Services;

use App\Events\ChatEvent;
use App\Events\V1\ChatUnseenCountEvent;
use App\Http\Resources\V1\Chat\ChatDetailsResource;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\RestrictedWords;
use App\Models\User;
use App\Notifications\V1\Chat\UnrestrictedChatNotification;
use App\Notifications\V1\Match\MatchedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ChatService extends BaseService
{
    protected $firebaseService;

    public function __construct(Chat $chat, FireBaseService $firebaseService)
    {
        $this->model = $chat;
        $this->firebaseService = $firebaseService;
    }

    public function getMessage($request)
    {
        $userId = auth()->id();
        $userType = auth()->user()->user_type;
        auth()
            ->user()
            ->unreadNotifications()
            ->where('type', MatchedNotification::class)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()->utc()]);
        $searchTerm = $request->name;
        $perPage = $request->has('per_page') ? $request->get('per_page') : '10';
        $rooms = ChatRoom::with([
            'chats',
            'createdBy.jobseeker',
            'createdBy.company',
            'user.company',
            'user.jobseeker',
            'match.jobseeker',
            'match.company',
            'match.jobseeker.user', // Adjust here to access the user relationship of jobseeker
            'match.company.user',
        ])
            ->select('chat_room.*')
            ->selectSub(
                $this->getUnseenCountQuery(auth()->user()->id),
                'unseen_count'
            )
            ->when(true, function ($query) use ($userId, $userType) {
                $query
                    ->where(function ($subquery) {
                        // Apply conditions for 'match' type
                        $subquery->orWhere(function ($matchTypeQuery) {
                            $matchTypeQuery
                                ->where('type', 'match')
                                ->whereHas('match', function ($matchQuery) {
                                    $matchQuery
                                        ->whereNotNull('matched')
                                        ->whereNull('unmatched');
                                });
                        });

                        // Apply conditions for 'request' type request
                        $subquery->orWhere(function ($requestTypeQuery) {
                            $requestTypeQuery
                                ->where('type', 'request')
                                ->whereHas('match', function ($matchQuery) {
                                    $matchQuery
                                        ->whereNull('matched')
                                        ->whereNull('unmatched')
                                        ->orWhereNotNull('matched');
                                });
                        });
                    })
                    ->where(function ($query) use ($userId, $userType) {
                        $this->addUserIdOrMatchQuery(
                            $query,
                            $userId,
                            $userType
                        );
                    });
            })

            ->when(!empty($searchTerm), function ($query) use ($searchTerm) {
                $this->addSearchTermQuery($query, $searchTerm);
            })
            ->leftJoin('chat', 'chat_room.id', '=', 'chat.chat_room_id')
            ->groupBy('chat_room.id')
            ->orderByRaw(
                'IFNULL(MAX(chat.created_at), chat_room.created_at) DESC'
            )
            ->paginate($perPage);

        return $rooms;
    }

    public function create($request)
    {
        // $data['type'] = $request->type;
        $room = ChatRoom::find($request->chat_room_id);
        $data['chat_room_id'] = $request->chat_room_id;
        $data['send_by'] = auth()->id();
        $data['message'] = $room->super_chat
            ? $request->message
            : $this->filter($request->message);
        if ($request->hasFile('file')) {
            $data['profile_img'] = $this->uploadImg($request->file, 'chat');
        }
        $reciver = chatroomReceiveBy($room);
        Log::info('receive user');
        Log::info($reciver);
        $isRoomOpen = $reciver
            ? isRoomOpen($reciver->id, $request->chat_room_id)
            : null;
        Log::info('room is true or false');
        Log::info($isRoomOpen);
        $data['seen'] = $isRoomOpen ? Carbon::now()->utc() : null;

        $output = $this->model->create($data);
        $room = $output->room;
        $receiveBy = $this->receiveUser($output);
        $totalUnseen = unseenCount($receiveBy);
        $chatEvent = [
            'room' => new ChatDetailsResource($output),
            'receiveBy' => $receiveBy->id,
        ];
        $unseen = [
            'receiveBy' => $receiveBy->id,
            'unseenCount' => $totalUnseen,
            'badgeCount' => badgeCount($receiveBy),
        ];
        broadcast(new ChatEvent($chatEvent));
        // Check if the result is empty, null, or false
        if (
            empty($isRoomOpen)
            || $isRoomOpen === null
            || $isRoomOpen === false
        ) {
            // Broadcast the ChatEvent
            // Broadcast the ChatUnseenCountEvent
            broadcast(new ChatUnseenCountEvent($unseen));
            // Check additional conditions and perform actions accordingly
            if ($receiveBy->device_token) {
                // If conditions met, send a notification
                $this->firebaseService->sendNotification($output, 'chat');
            }
        }

        return $output;
    }

    public function filter($message)
    {
        $badWords = RestrictedWords::pluck('word')->toArray();

        // Split the input comment into individual words
        $commentWords = explode(' ', $message);

        // Check each word and replace if necessary
        $filteredWords = array_map(function ($word) use ($badWords) {
            // Check if the lowercase version of the word is in the list of bad words
            if (
                in_array(strtolower($word), array_map('strtolower', $badWords))
            ) {
                return str_repeat('*', strlen($word)); // Replace with asterisks
            } else {
                return $word; // Keep the original word
            }
        }, $commentWords);

        // Display or store the modified comment
        $modifiedComment = implode(' ', $filteredWords);

        $patternPhoneNumber = '/([0-9]+[\- ]?[0-9]{6,})/';

        // Regular expression to match email addresses
        $patternEmail = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';

        // Regular expression to match social media links
        $patternSocialMedia =
            '/\b(?:https?:\/\/)?(?:www\.)?(?:twitter\.com\/\w+|facebook\.com\/\w+|instagram\.com\/\w+)\b/';

        // Remove phone numbers from the string
        $stringWithoutPhoneNumbers = preg_replace(
            $patternPhoneNumber,
            '**********',
            $modifiedComment
        );

        // Remove email addresses from the string
        $stringWithoutEmails = preg_replace(
            $patternEmail,
            '******',
            $stringWithoutPhoneNumbers
        );

        // Remove social media links from the string
        $stringWithoutSocialMedia = preg_replace(
            $patternSocialMedia,
            '',
            $stringWithoutEmails
        );

        // Display the result
        return $stringWithoutSocialMedia;
    }

    public function receiveUser($chat)
    {
        $room = ChatRoom::find($chat->chat_room_id);

        if ($room->type == 'direct') {
            $user =
                $chat->send_by == $room->created_by
                    ? $room->user
                    : $room->createdBy;
        } else {
            $user =
                $chat->send_by != $room->created_by
                    ? $room->createdBy
                    : ($room->match->jobseeker->user_id == $chat->send_by
                        ? $room->match->company->user
                        : $room->match->jobseeker->user);
        }

        return $user;
    }

    public function seen($chatroom)
    {
        auth()
            ->user()
            ->unreadNotifications()
            ->where('type', UnrestrictedChatNotification::class)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()->utc()]);

        return $chatroom
            ->chats()
            ->where('send_by', '!=', auth()->id())
            ->whereNull('seen')
            ->update(['seen' => Carbon::now()->utc()]);
    }

    public function getAllMessage()
    {
        $chats = Chat::with(['sendBy', 'room'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $chats;
    }

    public function directChatCount()
    {
        $currentMonth = Carbon::now()->format('Y-m');

        return $this->model
            ->join('chat_room', 'chat.chat_room_id', '=', 'chat_room.id')
            ->where('chat.send_by', auth()->id())
            ->where('chat_room.type', 'request')
            ->whereRaw('DATE_FORMAT(chat.created_at, "%Y-%m") = ?', [
                $currentMonth,
            ])
            ->count();
    }

    public function getUnseenCountQuery($id)
    {
        return Chat::selectRaw('COUNT(*)')
            ->whereColumn('chat_room_id', 'chat_room.id')
            ->whereNull('seen')
            ->where('send_by', '!=', $id);
    }

    public function getViolationExistsQuery($userIdColumn)
    {
        return function ($query) use ($userIdColumn) {
            $query->selectRaw("EXISTS (
                    SELECT 1
                    FROM violation_reports
                    WHERE user_id = $userIdColumn
                    AND status = 1
                    LIMIT 1
                )");
        };
    }

    public function getMatchViolationExistsQuery($matchingIdColumn)
    {
        return function ($query) use ($matchingIdColumn) {
            $query->selectRaw("EXISTS (
                    SELECT 1
                    FROM violation_reports
                    WHERE user_id = (
                        SELECT user_id
                        FROM matching
                        WHERE job_seeker_id = $matchingIdColumn
                        LIMIT 1
                    )
                    AND status = 1
                ) OR EXISTS (
                    SELECT 1
                    FROM violation_reports
                    WHERE user_id = (
                        SELECT user_id
                        FROM matching
                        WHERE company_id = $matchingIdColumn
                        LIMIT 1
                    )
                    AND status = 1
                )");
        };
    }

    public function addUserIdOrMatchQuery($query, $userId, $userType)
    {
        $query
            ->where(function ($orIdQuery) use ($userId) {
                $orIdQuery
                    ->where('created_by', $userId)
                    ->orWhere('user_id', $userId);
            })
            ->orWhere(function ($orMatchQuery) use ($userId, $userType) {
                $orMatchQuery->whereHas('match', function ($matchQuery) use (
                    $userId,
                    $userType
                ) {
                    $this->addMatchUserTypeQuery(
                        $matchQuery,
                        $userId,
                        $userType
                    );
                });
            });
    }

    protected function addSearchTermQuery($query, $searchTerm)
    {
        $query->where(function ($subQuery) use ($searchTerm) {
            if (auth()->user()->user_type == 2) {
                $subQuery->orWhereHas('match.jobseeker', function (
                    $jobseekerQuery
                ) use ($searchTerm) {
                    $jobseekerQuery->whereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ['%'.$searchTerm.'%']
                    );
                });
                $subQuery->orWhereHas('createdBy.jobseeker', function (
                    $jobseekerQuery
                ) use ($searchTerm) {
                    $jobseekerQuery->whereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ['%'.$searchTerm.'%']
                    );
                });
                $subQuery->orWhereHas('user.jobseeker', function (
                    $jobseekerQuery
                ) use ($searchTerm) {
                    $jobseekerQuery->whereRaw(
                        "CONCAT(first_name, ' ', last_name) LIKE ?",
                        ['%'.$searchTerm.'%']
                    );
                });
            } else {
                $subQuery->orWhereHas('match.company', function (
                    $companyQuery
                ) use ($searchTerm) {
                    $companyQuery->where(
                        'company_name',
                        'LIKE',
                        '%'.$searchTerm.'%'
                    );
                });
                $subQuery->orWhereHas('createdBy.company', function (
                    $companyQuery
                ) use ($searchTerm) {
                    $companyQuery->where(
                        'company_name',
                        'LIKE',
                        '%'.$searchTerm.'%'
                    );
                });
                $subQuery->orWhereHas('user.company', function (
                    $companyQuery
                ) use ($searchTerm) {
                    $companyQuery->where(
                        'company_name',
                        'LIKE',
                        '%'.$searchTerm.'%'
                    );
                });
            }
        });
    }

    public function addMatchUserTypeQuery($query, $userId, $userType)
    {
        if ($userType == 1) {
            $query->where('job_seeker_id', auth()->user()->jobseeker->id);
        } elseif ($userType == 2) {
            $query->where('company_id', auth()->user()->company->id);
        }
    }
}
