<?php

use App\Events\V1\ChatSeenEvent;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Models\Jobs;
use App\Models\Jobseeker;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

if (!function_exists('isRoomOpen')) {
    function isRoomOpen($userId, $roomId)
    {
        $room = 'connect_chatroom_' . $userId . '_' . $roomId;
        return Redis::exists($room) ? true : false;
    }
}
if (!function_exists('chatroomReceiveBy')) {
    function chatroomReceiveBy($room)
    {
        $room = ChatRoom::find($room->id);
        if (
            $room->match &&
            $room->chats()->exists() && $room->match->job_seeeker_id && $room->match->company_id &&
            $room->senderDetails !== null
        ) {
            $user =
                auth()->id() != $room->created_by
                    ? $room->createdBy
                    : ($room->match->jobseeker->user_id == auth()->id()
                        ? $room->match->company->user
                        : $room->match->jobseeker->user);
            return $user;
        }
        return null;
    }
}
if (!function_exists('chatroomNoMessage')) {
    function chatroomNoMessage($room)
    {
        $room = ChatRoom::find($room->id);

        if ($room->match && $room->match->company && $room->match->jobseeker) {
            return auth()->id() != $room->created_by
                ? $room->createdBy
                : ($room->match->jobseeker->user_id == auth()->id()
                    ? $room->match->company->user
                    : $room->match->jobseeker->user);
        }
        return null;
    }
}
if (!function_exists('chatSeenEvent')) {
    function chatSeenEvent($chatRoom)
    {
        if ($chatRoom->senderDetails) {
            $latseenTime = $chatRoom
                ->chats()
                ->where('send_by', $chatRoom->senderDetails)
                ->whereNotNull('seen')
                ->latest()
                ->value('seen');

            $data = [
                'userId' => $chatRoom->senderDetails,
                'roomId' => $chatRoom->id,
                'lastSeenTime' => Carbon::parse(
                    $latseenTime
                )->toIso8601ZuluString(),
            ];
            broadcast(new ChatSeenEvent($data));
        }
        return true;
    }
}
if (!function_exists('unseenCount')) {
    function unseenCount($user)
    {
        $userId = $user->id;
        $userType = $user->user_type;
        $rooms = Chat::selectRaw('COUNT(DISTINCT chat_room_id) as unseen_count')
            ->whereNotNull('chat_room_id')
            ->whereHas('room.match', function ($query) use ($user, $userType) {
                if ($userType == '1') {
                    // Assuming you have a jobseeker_id column in the room table
                    $query
                        ->whereNotNull('job_seeker_id')
                        ->where('job_seeker_id', $user->jobseeker->id);
                } else {
                    $query
                        ->whereNotNull('company_id')
                        ->where('company_id', $user->company->id);
                }
            })
            ->where('send_by', '!=', $userId)
            ->whereNull('seen')
            ->get();

        return $rooms->sum('unseen_count');
    }
}

if (!function_exists('chatRequestCheckUser')) {
    function chatRequestCheckUser($jobId, $jobseekerId)
    {
        if (auth()->user()->user_type == 1) {
            $job = Jobs::find($jobId);
            $user = $job->user()->exists() ? $job->user : null;
        } else {
            $jobseeker = Jobseeker::find($jobseekerId);
            $user = $jobseeker->user()->exists() ? $jobseeker->user : null;
        }
        return $user;
    }
}

?>
