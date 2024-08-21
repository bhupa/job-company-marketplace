<?php

namespace App\Http\Controllers\Api\V1\Matching;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Matching\ChatRequestStore;
use App\Http\Resources\V1\Matching\MatchingResource;
use App\Services\ChatService;
use App\Services\FireBaseService;
use App\Services\MatchingService;
use App\Services\V1\GlobalEventService;
use Google\Service\Logging\Resource\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatRequestController extends BaseController
{
    protected $matchingservice;

    protected $chat;

    // in this controller we have call the Matchingservice where all the model working will be perfrom on this service
    public function __construct(
        MatchingService $matchingservice,
        ChatService $chat
    ) {
        $this->matchingservice = $matchingservice;
        $this->chat = $chat;
    }
    public function chatRequest(ChatRequestStore $request)
    {
        try {
            $user =
                auth()->user()->user_type == 1
                    ? chatRequestCheckUser($request->job_id, null)
                    : chatRequestCheckUser(null, $request->job_seeker_id);
            if ($user) {
                $subscription = auth()->user()->subscribed_type;
                $totalChat = $this->chat->directChatCount();

                if (is_null($subscription)) {
                    $total = env('APP_ENV') == 'local' ? 10 : 1;
                    if ($totalChat >= $total) {
                        return $this->errors(
                            [
                                'message' =>
                                    'You have reached chat request limit for this month!!',
                                'type' => 'limit',
                            ],
                            400
                        );
                    }
                }

                $items = $this->matchingservice->chatRequest(
                    $request,
                    $subscription
                );
                $data = [
                    'chatRequest' => $this->chat->directChatCount(),
                    'items' => new MatchingResource($items),
                ];
                return $this->success($data, 'Matching done successfully');
            }
            return $this->success([], 'User has deleted their account.');
        } catch (\Exception $e) {
           
            return null;
          
        }
    }
}
