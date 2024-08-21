<?php

namespace App\Services;

use App\Http\Resources\V1\Chat\ChatDetailsPaginationResource;
use App\Http\Resources\V1\Matching\MatchingResource;
use App\Models\ChatRoom;

class ChatRoomService extends BaseService
{
    protected $chat;

    public function __construct(ChatRoom $room, ChatService $chat)
    {
        $this->model = $room;
        $this->chat = $chat;
    }

    public function getChatLists()
    {
        $userId = auth()->id();

        return $this->model
            ->where(function ($query) use ($userId) {
                $query
                    ->where('created_by', $userId)
                    ->orWhere('user_id', $userId);
            })
            ->with('chats', function ($query) {
                $query->with('payment')->orderBy('created_at', 'desc');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create($request, $type, $matching, $status)
    {
        $userId = $type == 'direct' ? $request->user_id : null;

        $chatRoom = [
            'type' => $type,
            'created_by' => auth()->id(),
            'user_id' => $userId,
            'matching_id' => $matching,
            'status' => $status,
        ];

        return $this->model->create($chatRoom);
    }

    public function update($request, $chat_room)
    {
        $data = $request->except('_token', 'image');
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImg($request->image, 'chatroom');
            $this->deleteImage($chat_room->image);
        }

        return tap($chat_room->update($data));
    }

    public function details($chatroom, $request)
    {
        $perPage = $request->has('per_page') ? $request->get('per_page') : '10';
        $chat_room = ChatRoom::with([
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
            ->select(['chat_room.*'])
            ->selectSub(
                $this->chat->getUnseenCountQuery(auth()->id()),
                'unseen_count'
            )
            ->find($chatroom->id);

        return [
            'id' => (string) $chat_room->id,
            'name' => $chat_room->name,
            'type' => $chat_room->type,
            'isAccepted' => $chat_room->match
                ? ($chat_room->match->matched
                    ? true
                    : false)
                : false,
            'image' => $chat_room->image
                ? url('/').'/storage/'.$chat_room->image
                : null,
            'status' => $chat_room->status,
            'unseen' => $chat_room->unseen_count,
            'lastSeenId' => $chat_room->lastSeenId
                ? (string) $chat_room->lastSeenId
                : null,
            'adminAssit' => $chat_room->admin_assist ? true : false,
            'isDeleted' => $chat_room->created_by ? false : true,
            'superChat' => $chat_room->super_chat ? true : false,
            'isChatViolation' => $chat_room->violation
                ? ($chat_room->violation->status
                    ? true
                    : false)
                : false,
            'payment_id' => (string) $chat_room->payment_id,
            'matchedUser' => $chat_room->getMatchedUserAttribute(
                auth()->user()
            ),
            'createdBy' => [
                'userId' => (string) $chat_room->created_by,
                'userType' => $chat_room->createdBy
                    ? $chat_room->createdBy->user_type
                    : null,
                'id' => $chat_room->created_by
                    ? ($chat_room->createdBy->user_type == 1
                        ? (string) $chat_room->createdBy->jobseeker->id
                        : (string) $chat_room->createdBy->company->id)
                    : null,
                'name' => $chat_room->created_by
                    ? ($chat_room->createdBy->user_type == 1
                        ? $chat_room->createdBy->jobseeker->first_name.
                            ' '.
                            $chat_room->createdBy->jobseeker->last_name
                        : $chat_room->createdBy->company->company_name)
                    : null,
                'image' => $chat_room->created_by
                    ? ($chat_room->createdBy->user_type == 1
                        ? url('/').
                            '/storage/'.
                            $chat_room->createdBy->jobseeker->profile_img
                        : url('/').
                            '/storage/'.
                            $chat_room->createdBy->company->logo)
                    : null,
            ],
            'user' => [
                'userId' => $chat_room->user_id
                    ? (string) $chat_room->user_id
                    : null,
                'userType' => $chat_room->user_id
                    ? $chat_room->user->user_type
                    : null,
                'id' => $chat_room->user_id
                    ? ($chat_room->user->user_type == 1
                        ? (string) $chat_room->user->jobseeker->id
                        : (string) $chat_room->user->company->id)
                    : null,
                'name' => $chat_room->user_id
                    ? ($chat_room->user->user_type == 1
                        ? $chat_room->user->jobseeker->first_name.
                            ' '.
                            $chat_room->user->jobseeker->last_name
                        : $chat_room->user->company->company_name)
                    : null,
                'image' => $chat_room->user_id
                    ? ($chat_room->user->user_type == 1
                        ? url('/').
                            '/storage/'.
                            $chat_room->user->jobseeker->profile_img
                        : url('/').
                            '/storage/'.
                            $chat_room->user->company->logo)
                    : null,
            ],
            'match' => $chat_room->matching_id
                ? new MatchingResource($chat_room->match)
                : null,
            'chats' => new ChatDetailsPaginationResource(
                $chatroom
                    ->chats()
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage)
            ),
        ];
    }

    public function getRoomDetails($column, $value)
    {
        return $this->model->where->where($column, $value)->first();
    }
}
