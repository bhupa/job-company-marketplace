<?php

namespace App\Services;

use App\Events\MatchEventRequest;
use App\Events\MatchingRequestCountEvent;
use App\Events\V1\ChatRefreshEvent;
use App\Http\Resources\V1\Matching\Pagination\FavouritePaginationResource;
use App\Http\Resources\V1\Matching\Pagination\MatchingPaginationResource;
use App\Http\Resources\V1\Matching\Pagination\MatchingReceivePagintationResource;
use App\Http\Resources\V1\Matching\Pagination\MatchingSentPaginationResource;
use App\Models\Chat;
use App\Models\Flip;
use App\Models\Jobs;
use App\Models\Jobseeker;
use App\Models\Matching;
use App\Services\V1\GlobalEventService;
use App\Traits\MatchTrait;
use Carbon\Carbon;

class MatchingService extends BaseService
{
    use MatchTrait;
    protected $fireBaseService;
    protected $room;
    protected $chat;
    protected $matching;
    protected $globalEvent;

    public function __construct(
        Matching $matching,
        FireBaseService $fireBaseService,
        ChatRoomService $room,
        ChatService $chat,
        GlobalEventService $globalEvent
    ) {
        $this->model = $matching;
        $this->fireBaseService = $fireBaseService;
        $this->room = $room;
        $this->chat = $chat;
        $this->globalEvent = $globalEvent;
    }

    // this function isused  for sending the matching request
    public function create($request, $subscription)
    {
        $data = '';
        $matched = [];
        $matching = [];
        $jobs = [];
        $jobseekers = [];
        foreach ($request->type as $key => $type) {
            if (auth()->user()->user_type === 1) {
                $jobseekerId = auth()->user()->jobseeker->id;
                $job = Jobs::find($request->job_id[$key]);
                $match = $this->checkCompanyAndEmployer(
                    $type,
                    auth()->user()->jobseeker->id,
                    $job->user->company->id,
                    $job->id,
                    'match'
                );
                $data = empty($match)
                    ? $this->common($jobseekerId, $job)
                    : null;
            } else {
                $match = $this->checkCompanyAndEmployer(
                    $type,
                    $request->job_seeker_id[$key],
                    auth()->user()->company->id,
                    null,
                    'match'
                );
                $data = empty($match)
                    ? $this->common($request->job_seeker_id[$key], null)
                    : null;
            }

            $matRequest = $this->countRequest();
            $userRequest = $matRequest['userRequest'];
            if ($type == 1 && !empty($match)) {
                $matchedData = $this->handleMatchedType($match);
                $matched[] = $matchedData;
            }

            if (
                $this->shouldCreateMatch(
                    $data,
                    $type,
                    $subscription,
                    $userRequest,
                    $match
                )
            ) {
                $match = $this->createMatch($data);
                $matching[] = $match;
                $this->handleMatchCreation($subscription, $userRequest);
            } else {
                $matching[] = $match;
            }

            $this->handleRejectedType(
                $type,
                $request,
                $jobs,
                $jobseekers,
                $key,
                'match'
            );
        }
        $this->handleLeftSwipe($jobs, $jobseekers);

        // (auth()->user()->user_type === 1 &&  !empty($jobs)) &&   setLeftSwipeJobs($jobs);
        // (auth()->user()->user_type === 2 &&  !empty($jobseekers)) && setLeftSwipeJobseekers($jobseekers);
        return ['matching' => $matching, 'matched' => $matched];
    }

    // this function isused to check the employer exists in matching database or not with give field
    public function checkCompanyAndEmployer(
        $type,
        $joseekerId,
        $companyId,
        $jobId,
        $matchType
    ) {
        $query = Matching::query();

        if (auth()->user()->user_type == 1) {
            $result = Matching::where('job_seeker_id', $joseekerId)
                ->where('company_id', $companyId)
                ->whereNull('matched')
                ->whereNull('unmatched')
                ->first();

            if (
                $result
                && empty($result->job_id)
                && $result->created_by !== auth()->id()
            ) {
                tap($result->update(['job_id' => $jobId]));
            }
            if (
                $result
                && !empty($result->job_id)
                && $result->job_id !== $jobId
            ) {
                $result = null;
            }
        } else {
            $result = $query
                ->where('job_seeker_id', $joseekerId)
                ->where('company_id', $companyId)
                ->whereNull('matched')
                ->whereNull('unmatched')
                ->first();
        }

        return $result;
    }

    // thisfunction is used for add and remove the favourite lists
    public function favourite($request, $subscription)
    {
        if (auth()->user()->user_type === 1) {
            $job = Jobs::find($request->job_id);
            $companyId = $job->user->company->id;
            $jobseekerId = auth()->user()->jobseeker->id;
            $jobId = $job->id;
        } else {
            $companyId = auth()->user()->company->id;
            $jobseekerId = (int) $request->job_seeker_id;
            $jobId = null;
        }
        $favouriteBy = auth()->id();
        $createdBy = auth()->id();
        $result = [
            'company_id' => $companyId,
            'job_seeker_id' => $jobseekerId,
            'favourite_by' => $favouriteBy,
            'created_by' => $createdBy,
            'job_id' => $jobId,
        ];

        $old = Matching::where($result)->first();
        $results = $old;

        if (empty($subscription) && $request->favourite == 1) {
            Flip::create([
                'user_id' => auth()->user()->id,
                'flip' => 1,
                'type' => 'favourite',
            ]);
        }
        if (empty($old) && $request->favourite == 1) {
            $output = $this->model->create($result);
            $results = $output;
            $results['message'] = 'Added to the favourite lists';
        }

        if ($old && $request->favourite == 0) {
            // if ($old->relationLoaded('child')) {
            //     $old->child->delete();
            // }
            $old ? $old->delete() : null;
            $results['message'] = 'Remove from favourite lists';
        }

        return $results;
    }

    // this function is used for matching accept and refuse
    public function accept($request, $match)
    {
        $matching = $this->model->find($match->id);
        $user = $this->getUser($matching);
        if ($request->type === 'accept') {
            $data['matched'] = Carbon::today();
            $data['favourite_by'] = null;

            if (empty($matching->room)) {
                $request['user_id'] = null;
                $this->room->create($request, 'match', $matching->id, 1);
            } else {
                $matching->room->update(['status' => 1]);
            }
            // when accept create the chat message
            $deviceToekn =
                $matching->createdBy->user_type == 2
                    ? $matching->company->user->device_token
                    : $matching->jobseeker->user->device_token;
            $deviceToekn
                && $this->fireBaseService->sendNotification($matching, 'matched');
        } else {
            if ($matching->room->exists) {
                $matching->room->chats
                    ? $matching->room->chats()->delete()
                    : null;
                $matching->room->delete();
            }
            $data['unmatched'] = Carbon::today();
        }
        $user && broadcast(new MatchEventRequest(['user_id' => $user->id]));
        $user && broadcast(new ChatRefreshEvent(['user_id' => $user->id]));
        // ($matching->parent_id) ?  $matching->parent->update(['unmatched'=> Carbon::today()]) : null;
        tap($matching->update($data));

        return $this->model->with('room')->find($matching->id);
    }

    public function common($jobseekerId, $job)
    {
        if (auth()->user()->user_type === 1) {
            $data['job_id'] = $job->id;
            $data['company_id'] = $job->user->company->id;
            $data['job_seeker_id'] = $jobseekerId;
        } else {
            $data['company_id'] = auth()->user()->company->id;
            $data['job_seeker_id'] = $jobseekerId;
        }
        $data['created_by'] = auth()->id();

        return $data;
    }
    // this function is used to get all the request of the matching

    public function getAllsentRequest($request, $type)
    {
        $perPage = $request->has('per_page') ? $request->get('per_page') : '30';
        $query = new Matching();
        $column =
            auth()->user()->user_type == 1 ? 'job_seeker_id' : 'company_id';
        $value =
            auth()->user()->user_type == 1
                ? auth()->user()->jobseeker->id
                : auth()->user()->company->id;
        if ($type == 'created_by') {
            return $this->sent($query, $type, $perPage);
        } elseif ($type == 'received') {
            return $this->received($query, $value, $column, $perPage);
        } elseif ($type == 'match') {
            return $this->matched($query, $column, $perPage);
        } elseif ($type == 'favourite_by') {
            return $this->favourites($query, $type, $perPage);
        }

        return $query;
    }

    // this function will fetch all the matching received
    public function received($query, $value, $column, $perPage)
    {
        $query = $query
            ->with([
                'company.jobs' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->where('created_by', '!=', auth()->user()->id)
            ->whereNull('matched')
            ->whereNull('unmatched')
            ->whereNull('favourite_by')
            ->whereNotNull('company_id')
            ->whereNotNull('job_seeker_id')
            ->where($column, $value)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
        // $query = $output->paginate($perPage);
        $subscription = auth()
            ->user()
            ->subscriptions()
            ->where('stripe_status', 'active')
            ->where('ends_at', '>', Carbon::now())
            ->first();

        if ($subscription) {
            $data = [
                'matched' => 0,
                'unmatched' => 0,
                'items' => new MatchingReceivePagintationResource($query),
            ];
        } else {
            $data = [
                'matched' => Matching::where(
                    'created_by',
                    '!=',
                    auth()->user()->id
                )
                    ->where($column, $value)
                    ->whereNotNull('matched')
                    ->whereNotNull('company_id')
                    ->whereNotNull('job_seeker_id')
                    ->count(),
                'unmatched' => Matching::where(
                    'created_by',
                    '!=',
                    auth()->user()->id
                )
                    ->where($column, $value)
                    ->whereNotNull('unmatched')
                    ->whereNotNull('company_id')
                    ->whereNotNull('job_seeker_id')
                    ->count(),
                'items' => new MatchingReceivePagintationResource($query),
            ];
        }
        $data['totalRequest'] = $this->globalEvent->count();

        return $data;
    }

    // this function will fetch all the matching request to user
    public function sent($query, $type, $perPage)
    {
        $output = $query
            ->with([
                'company',
                'company.user',
                'company.jobs' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->where('created_by', '=', auth()->user()->id)
            ->whereNull('favourite_by')
            ->orderBy('created_at', 'desc')
            ->whereNotNull('company_id')
            ->whereNotNull('job_seeker_id')
            ->paginate($perPage);

        $data = [
            'matched' => 0,
            'unmatched' => 0,
            'items' => new MatchingSentPaginationResource($output),
        ];

        return $data;
    }

    // this function will fetch all the matching jobs and company forjobseeker and jobseeker for company
    public function matched($query, $column, $perPage)
    {
        $userId =
            auth()->user()->user_type == 1
                ? auth()->user()->jobseeker->id
                : auth()->user()->company->id;

        $query = $query
            ->with(['company', 'job'])
            ->whereNull('favourite_by')
            ->whereNotNull('matched')
            ->whereNotNull('company_id')
            ->whereNotNull('job_seeker_id')
            ->where(function ($query) use ($column, $userId) {
                $query->where($column, $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = [
            'matched' => 0,
            'unmatched' => 0,
            'items' => new MatchingPaginationResource($query),
        ];

        return $data;
    }

    // this function will fetch all the favourite lists of the login user
    public function favourites($query, $type, $perPage)
    {
        $query = $query
            ->with(['job'])
            ->where('favourite_by', auth()->id())
            ->whereNull('matched')
            ->whereNull('unmatched')
            ->whereNotNull('company_id')
            ->whereNotNull('job_seeker_id')
            // ->with('child')
            ->where($type, auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $data = [
            'matched' => 0,
            'unmatched' => 0,
            'items' => new FavouritePaginationResource($query),
        ];

        return $data;
    }

    // this function is used to send chat request
    public function chatRequest($request, $subscription)
    {
        $matRequest = $this->countRequest();
        $userRequest = $matRequest['userRequest'];

        $data = $request->except('_token', 'message');
        $data['favourite_by'] = null;
        $data['created_by'] = auth()->id();
        if (auth()->user()->user_type == 2) {
            $data['company_id'] = auth()->user()->company->id;
            $jobseeker = Jobseeker::find($request->job_seeker_id);
            $user = $jobseeker->user;
            $user['name'] = auth()->user()->company->company_name;
            $match = $this->checkCompanyAndEmployer(
                1,
                $request->job_seeker_id,
                auth()->user()->company->id,
                null,
                'request'
            );
        } else {
            $job = Jobs::find($request->job_id);
            $data['company_id'] = $job->user->company->id;
            $data['job_seeker_id'] = auth()->user()->jobseeker->id;
            $user = $job->user;
            $user['name'] =
                auth()->user()->jobseeker->first_name.
                ' '.
                auth()->user()->jobseeker->last_name;
            $match = $this->checkCompanyAndEmployer(
                1,
                auth()->user()->jobseeker->id,
                $job->user->company->id,
                $job->id,
                'request'
            );
        }

        $request['message'] = $request->message
            ? $request->message
            : "Let's get connected!";
        $request['user_id'] = null;
        if (empty($match)) {
            $output = $this->model->create($data);
            $chatroom = $this->room->create(
                $request,
                'request',
                $output->id,
                0
            );
            $chatroomId = $chatroom->id;
            tap($chatroom->update(['status' => 0]));
            $request['chat_room_id'] = $chatroomId;
            $this->chat->create($request);
            $user['roomId'] = $chatroom->id;
            $user->device_token
                && $this->fireBaseService->sendOtp($user, 'chat-request', $output);
            $matchedEventReceiveUser = $user;
            $result = $output;
        } else {
            $matchedUser = $this->getUser($match);
            if ($match->room()->exists()) {
                $request['chat_room_id'] = $match->room->id;
            } else {
                $chatroom = $this->room->create(
                    $request,
                    'request',
                    $match->id,
                    0
                );
                $request['chat_room_id'] = $chatroom->id;
            }
            $this->chat->create($request);
            broadcast(new MatchEventRequest(['user_id' => auth()->id()]));
            $matchedUser
                && broadcast(
                    new ChatRefreshEvent(['user_id' => $matchedUser->id])
                );
            tap($match->update(['matched' => Carbon::today()]));
            $matchedEventReceiveUser = $matchedUser;
            $result = $match;
            $matchEvent = [
                'user_id' => $matchedEventReceiveUser->id,
                'totalRequest' => $this->globalEvent->count(),
            ];
            broadcast(new MatchingRequestCountEvent($matchEvent));
            $matchedEventReceiveUser->device_token
                && $this->fireBaseService->sendNotification($result, 'matched');
            auth()->user()->device_token
                && $this->fireBaseService->sendNotification(
                    $match,
                    'chat-request-matched'
                );
        }

        if (empty($subscription) || $userRequest) {
            Flip::create([
                'user_id' => auth()->user()->id,
                'flip' => 1,
                'type' => 'request',
            ]);
        }
        $matchRequestEvent = [
            'user_id' => auth()->id(),
            'totalRequest' => $this->globalEvent->count(),
        ];
        broadcast(new MatchingRequestCountEvent($matchRequestEvent));
        broadcast(
            new MatchEventRequest(['user_id' => $matchedEventReceiveUser->id])
        );

        return $result;

        // }
    }

    public function matchedWhenBothSwipeRight($type, $match)
    {
        if ($type == 1) {
            tap($match)->update(['matched' => Carbon::today()]);
            $this->room->create(null, 'match', $match->id, 1);
        }

        return $match;
    }

    public function createFlipCount($subscription, $userRequest)
    {
        if (empty($subscription) || $userRequest) {
            Flip::create([
                'user_id' => auth()->user()->id,
                'flip' => 1,
                'type' => 'request',
            ]);
        }
    }

    public function matchingRequestCountEvent($match)
    {
        $userId = $this->getUserId($match);
        $broadCast = [
            'user_id' => $userId,
            'totalRequest' => $this->globalEvent->count(),
        ];
        broadcast(new MatchingRequestCountEvent($broadCast));

        return $match;
    }

    public function getUserId($match)
    {
        if (auth()->user()->user_type == 1) {
            $userId = $match->company->user_id;
        } else {
            $userId = $match->jobseeker->user_id;
        }

        return $userId;
    }

    public function getUser($match)
    {
        if (auth()->user()->user_type == 1) {
            $user = $match->company->user;
        } else {
            $user = $match->jobseeker->user;
        }

        return $user;
    }
}
