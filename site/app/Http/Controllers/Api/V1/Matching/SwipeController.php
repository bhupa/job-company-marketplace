<?php

namespace App\Http\Controllers\Api\V1\Matching;

use App\Events\MatchingRequestCountEvent;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Matching\MatchingAcceptRequest;
use App\Http\Requests\V1\Matching\MatchingFavouriteRequest;
use App\Http\Requests\V1\Matching\MatchingStoreRequest;
use App\Http\Resources\V1\Matching\FavouriteResource;
use App\Http\Resources\V1\Matching\MatchingResource;
use App\Models\Matching;
use App\Services\ChatService;
use App\Services\FireBaseService;
use App\Services\MatchingService;
use App\Services\V1\GlobalEventService;
use Illuminate\Http\Request;

class SwipeController extends BaseController
{
    protected $matchingservice;
    protected $firebaseservice;
    protected $chat;
    protected $globalEvent;

    // in this controller we have call the Matchingservice where all the model working will be perfrom on this service
    public function __construct(
        MatchingService $matchingservice,
        FireBaseService $firebaseservice,
        ChatService $chat,
        GlobalEventService $globalEvent
    ) {
        $this->matchingservice = $matchingservice;
        $this->firebaseservice = $firebaseservice;
        $this->chat = $chat;
        $this->globalEvent = $globalEvent;
    }

    // this function is used to fetch the received,sent,favourit and matching lists of user
    public function index(Request $request)
    {
        $type = '';
        $message = '';
        if (request()->has('type')) {
            if (request()->get('type') === 'sent') {
                $type = 'created_by';
                $message = 'Sent request';
            } elseif (request()->get('type') === 'received') {
                $type = 'received';
                $message = 'Received request';
            } elseif (request()->get('type') === 'match') {
                $type = 'match';
                $message = 'Matching lists';
            } elseif (request()->get('type') === 'favourite') {
                $type = 'favourite_by';
                $message = 'Favourite lists';
            }

            $matching = $this->matchingservice->getAllsentRequest(
                $request,
                $type
            );

            return $this->success($matching, $message);
        }

        return $this->errors(
            ['message' => 'Type field is required to fetch matching lists'],
            400
        );
    }

    // this function wil send the matching request
    public function store(MatchingStoreRequest $request)
    {
        $leftSwipe = [];
        foreach ($request->type as $key => $type) {
            if ($type === '0') {
                if (auth()->user()->user_type === 2) {
                    $leftSwipe[] = $request->job_seeker_id[$key];
                } else {
                    $leftSwipe[] = $request->job_id[$key];
                }
            }
        }
        try {
            $subscription = auth()->user()->subscribed_type;
            $matRequest = $this->matchingservice->countRequest();
            $userRequest = $matRequest['userRequest'];

            if (is_null($subscription)) {
                $total = env('APP_ENV') == 'local' ? 10 : 10;
                if ($userRequest >= $total) {
                    if (empty($leftSwipe)) {
                        return $this->errors(
                            [
                                'message' => 'You have reached limit for the day!',
                                'type' => 'limit',
                            ],
                            400
                        );
                    }
                }
            }
            $output = $this->matchingservice->create($request, $subscription);
            $nonNullItemsMatched = [];
            $nonNullItemsMatching = [];
            if (is_array($output['matching'])) {
                $nonNullItemsMatching = array_filter(
                    $output['matching'],
                    function ($item) {
                        return $item !== null; // Filter out null items
                    }
                );
            }
            if (is_array($output['matched'])) {
                $nonNullItemsMatched = array_filter(
                    $output['matched'],
                    function ($item) {
                        return $item !== null; // Filter out null items
                    }
                );
            }
            $matRequestend = $this->matchingservice->countRequest();

            $countFavourite = $matRequestend['countFavourite'];
            $userRequestend = $matRequestend['userRequest'];
            $data = [
                'favouriteCount' => $countFavourite ? $countFavourite : 0,
                'dailyCount' => $userRequestend ? $userRequestend : 0,
                'items' => MatchingResource::collection(
                    collect($nonNullItemsMatching)
                ),
                'matchedData' => MatchingResource::collection(
                    collect($nonNullItemsMatched)
                ),
            ];

            return $this->success($data, 'Matching done successfully');
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    // this function is used to add the favourite lists to user
    public function favourite(MatchingFavouriteRequest $request)
    {
        try {
            $subscription = auth()->user()->subscribed_type;
            if ($request->favourite == 1) {
                $matRequest = $this->matchingservice->countRequest();
                $countFavourite = $matRequest['countFavourite'];
                $userRequest = $matRequest['userRequest'];

                if (is_null($subscription)) {
                    $total = env('APP_ENV') == 'local' ? 10 : 10;
                    if ($userRequest > $total || $countFavourite > $total) {
                        return $this->errors(
                            [
                                'message' => 'You have reached lifetime limit for favorite!',
                                'type' => 'limit',
                            ],
                            400
                        );
                    }
                }
            }

            $output = $this->matchingservice->favourite(
                $request,
                $subscription
            );
            $matRequestend = $this->matchingservice->countRequest();
            $countFavouritend = $matRequestend['countFavourite'];
            $userRequestend = $matRequestend['userRequest'];
            $data = [
                'favouriteCount' => $countFavouritend ? $countFavouritend : 0,
                'dailyCount' => $userRequestend ? $userRequestend : 0,
                'items' => new FavouriteResource($output),
            ];

            return $this->success($data, $output['message']);
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    // this function is used to get the accept and refusethe mathing
    public function accept(MatchingAcceptRequest $request, Matching $matching)
    {
        try {
            $output = $this->matchingservice->accept($request, $matching);

            $message =
                $request->type == 'accept'
                    ? 'Request accepted succesfully'
                    : 'Request refuse succesfully';
            $result = [
                'user_id' => auth()->id(),
                'totalRequest' => $this->globalEvent->count(),
            ];
            broadcast(new MatchingRequestCountEvent($result));

            return $this->success(new MatchingResource($output), $message);
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function count()
    {
        try {
            $result = [
                'user_id' => auth()->id(),
                'totalRequest' => $this->globalEvent->count(),
            ];

            return $this->success([
                'totalRequest' => $result,
                'Total count request',
            ]);
        } catch (\Throwable $th) {
            return $this->errors(['message' => $th->getMessage()], 400);
        }
    }
}