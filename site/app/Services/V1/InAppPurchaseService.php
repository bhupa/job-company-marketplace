<?php

namespace App\Services\V1;

use App\Events\V1\SuperChatEvent;
use App\Models\ChatRoom;
use App\Models\Payment;
use App\Models\V1\InAppPurchase;
use App\Services\BaseService;
use App\Traits\SubscriptionsTrait;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InAppPurchaseService extends BaseService
{
    public function __construct(InAppPurchase $inappPurchase)
    {
        $this->model = $inappPurchase;
    }

    public function create($request)
    {
        $key = env('APP_ENV');
        $currentDate = Carbon::now();
        $paymentFor = 'subscription';
        if (Str::contains($request->item_id, 'one_week') || Str::contains($request->item_id, 'oneweek')) {
            $ends_at = $currentDate->addWeeks(1);
            $paymentFor = 'subscription';
            // } elseif ($request->item_id == 'two-week' || $request->item_id == 'company_two_week') {
            //     $ends_at = $currentDate->addWeeks(2);
        } elseif (Str::contains($request->item_id, 'one_month') || Str::contains($request->item_id, 'onemonth')) {
            $ends_at = $currentDate->addMonths(1);
            $paymentFor = 'subscription';
        } elseif (Str::contains($request->item_id, 'three_months') || Str::contains($request->item_id, 'threemonths')) {
            $ends_at = $currentDate->addMonths(3);
            $paymentFor = 'subscription';
        } elseif (Str::contains($request->item_id, 'six_months') || Str::contains($request->item_id, 'sixmonths')) {
            $ends_at = $currentDate->addMonths(6);
            $paymentFor = 'subscription';
        } elseif (Str::contains($request->item_id, 'super_chat')) {
            $ends_at = null;
            $paymentFor = 'super_chat';
        }

        $subscriptionType = $request->has('subscriptions_type')
            ? $request->subscriptions_type
            : 'iap';
        $data = $request->except('_token');
        $data['user_id'] = $request->has('user_id')
            ? $request->user_id
            : auth()->id();
        $data['payment_for'] = $paymentFor;
        $data['status'] = 'pending';
        $data['store_user_id'] = $request->uniqueIdentifier;
        // $data['ends_at']=($ends_at) ? $ends_at->toDateTimeString() :null;

        auth()
            ->user()
            ->update(['subscriptions_type' => $subscriptionType]);
        if ($request->payment_type == 'apple') {
            // Create a new record for Apple in-app purchase
            $output = $this->model->create($data);
        } else {
            // Retrieve the latest Google in-app purchase record
            $output = InAppPurchase::where('payment_type', 'google')
                ->where('item_id', $request->item_id)
                ->where('store_user_id', $request->uniqueIdentifier)
                ->latest()
                ->first();

            // If a record exists, update it with the new information
            if ($output) {
                tap($output)->update([
                    'user_id' => auth()->id(),
                    'purchase_token' => $request->purchase_token,
                    'transaction_receipt' => $request->transaction_receipt,
                ]);
            } else {
                $output = $this->model->create($data);
            }
        }

        if ($request->has('chat_room_id')) {
            $room = ChatRoom::find($request->chat_room_id);
            if ($room) {
                $room->update([
                    'payment_type' => 'iap',
                    'in_app_id' => $output->id,
                ]);
                $receiveBy = chatroomReceiveBy($room);
                if ($receiveBy) {
                    $event = [
                        'receiveBy' => $receiveBy->id,
                        'chatRoomId' => (string) $room->id,
                    ];
                    broadcast(new SuperChatEvent($event));
                }
            }
        }

        return $output;
    }
}
