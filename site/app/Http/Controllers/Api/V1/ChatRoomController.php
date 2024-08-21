<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Chat\ChatRoomSetRequest;
use App\Http\Requests\V1\Chat\ChatRoomStoreRequest;
use App\Http\Requests\V1\Chat\ChatRoomUpdateRequest;
use App\Http\Resources\V1\Chat\ChatRoomDetailsResource;
use App\Mail\V1\CompanyUnrestrictedChatEmail;
use App\Mail\V1\JobseekerUnrestrictedChatEmail;
use App\Models\Chat;
use App\Models\ChatRoom;
use App\Services\ChatRoomService;
use App\Services\ChatService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Stripe\Checkout\Session;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class ChatRoomController extends BaseController
{
    protected $room;
    protected $chat;

    public function __construct(ChatRoomService $room, ChatService $chat)
    {
        $this->room = $room;
        $this->chat = $chat;
    }

    public function store(ChatRoomStoreRequest $request)
    {
        try {
            $subscription = auth()->user()->subscribed_type;
            if (is_null($subscription)) {
                $currentMonth = Carbon::now()->month;
                $monthlyChat = $this->chat->directChatCount();
                if ($monthlyChat >= 1) {
                    return $this->errors(
                        [
                            'message' => trans(
                                'lang.chat.limit',
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
            $room['unseen_count'] = Chat::whereNull('seen')
                ->where('chat_room_id', $room->id)
                ->count();
            $room['chats'] = $room->chats;

            return $this->success(
                new ChatRoomDetailsResource($room),
                'Chat room created successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function update(ChatRoomUpdateRequest $request, ChatRoom $chat_room)
    {
        try {
            $this->room->update($request, $chat_room);
            $room = $this->room->find($chat_room->id);

            $room['unseen_count'] = Chat::whereNull('seen')
                ->where('chat_room_id', $room->id)
                ->count();
            $room['chats'] = $room->chats;

            return $this->success(
                new ChatRoomDetailsResource($room),
                'Chat room updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => $e->getMessage(), 'type' => 'default'],
                400
            );
        }
    }

    public function show(Request $request, ChatRoom $chat_room)
    {
        try {
            ($chat_room->match && $chat_room->match->matched) && $this->chat->seen($chat_room);
            $output = $this->room->details($chat_room, $request);
            totalbadgeCount(auth()->user());
            $receiveBy = chatroomReceiveBy($chat_room);
           
            $receiveBy
                && ($chat_room->chats()->exists()
                    && isRoomOpen(auth()->id(), $chat_room->id)
                    && isRoomOpen($receiveBy->id, $chat_room->id))
                && chatSeenEvent($chat_room);

            return $this->success($output, 'Chat room  details');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(ChatRoom $chat_room)
    {
        try {

            (auth()->user()->user_type == 1) ? $chat_room->match->update(['job_seeker_id'=>null]) : 
            $chat_room->match->update(['company_id'=>null,'job_id'=>null]);
            ( $chat_room->created_by === auth()->id()) &&  $chat_room->update(['created_by'=>null]);

            return $this->success([], 'Chat has been deleted successfully');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function setChatRoom(ChatRoomSetRequest $request)
    {
        try {
            $userId = $request->userId;
            $roomId = $request->roomId;
            $type = $request->type;
            $key = 'connect_chatroom_'.$userId.'_'.$roomId;
            if ($type == 'open') {
                Redis::set($key, $roomId);
            } else {
                Redis::del($key, $roomId);
            }
            $message =
                $type == 'open'
                    ? 'Chatroom joined successfully'
                    : 'Chatroom left successfully';

            return $this->success($type, $message);
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function superChatEmail($id, Request $request)
    {
        $room = $this->room->find($id);

        if ($room) {
            $user = auth()->user();
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $products = Product::all(['limit' => 100]); // Adjust the limit as needed

            // Filter products that have metadata containing 'super_chat'
            $superChatId = null; // Initialize the variable to store the super chat product

            foreach ($products as $product) {
                if ($product->metadata['name'] === 'super_chat') { // Replace 'prod_Pr4gVlg3cW3RxA' with the actual ID of the product
                    $superChatId = $product;
                    break; // Exit the loop once the product is found
                }
            }
            $prices = Price::all([
                'product' => $superChatId,
            ]);

            $newoutput = '';
            foreach ($prices as $pdPrice) {
                if ($pdPrice->currency == 'jpy') {
                    $newoutput = $pdPrice->id;
                } else {
                    $newoutput = $pdPrice->id;
                }
            }
            if (is_null($user->stripe_id)) {
                // If the user doesn't have a Stripe customer ID, create one
                $stripeCustomer = $user->createAsStripeCustomer();
            } else {
                // If the user already has a Stripe customer ID, retrieve it
                $stripeCustomer = $user->asStripeCustomer();
            }

            $session = Session::create([
                'customer' => $stripeCustomer->id,
                'line_items' => [
                    [
                        'price' => $newoutput,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'metadata' => [
                    'user_id' => $user->id,
                    'chatroom_id' => $room->id,
                    'type' => 'chat',
                ],
                'success_url' => env('APP_URL').'profile?success=true',
                'cancel_url' => env('APP_URL').'profile?canceled=true',
            ]);
            if (!$request->has('type') && $request->get('type') !== 'web') {
                $user->user_type == 1
                    ? Mail::to($user->email)->send(
                        new JobseekerUnrestrictedChatEmail(
                            $user,
                            $room,
                            $session->url
                        )
                    )
                    : Mail::to($user->email)->send(
                        new CompanyUnrestrictedChatEmail(
                            $user,
                            $room,
                            $session->url
                        )
                    );
            } else {
                return $this->success(['url' => $session->url], 'Stripe Url');
            }

            return $this->success($user, 'Mail send');
        }

        return $this->errors(['message' => 'Chat room not exists'], 400);
    }
}
