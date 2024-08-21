<?php

namespace App\Admin\Actions;

use App\Events\V1\SuperChatEvent;
use App\Models\ChatRoom;
use App\Models\User;
use App\Models\V1\InAppPurchase;
use App\Services\V1\InAppPurchaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use OpenAdmin\Admin\Actions\RowAction;
use OpenAdmin\Admin\Form;
use Illuminate\Support\Str;

class AddSubscription extends RowAction
{
    public $name = 'add_subscription';

    public $icon = 'icon-plus';

    public function handle(InAppPurchase $model, Request $request)
    {
         $user = User::find($this->getKey());
        
        if($user->subscriptions_type == 'iap'){

        }
         $inapp = InAppPurchase::find($this->getKey());

        // Fill the model with data from the request

        $currentDate = Carbon::now();
        if (Str::contains($inapp->item_id, 'one_week')) {
            $ends_at = $currentDate->addWeeks(1);
            $paymentFor = 'subscription';
            // } elseif ($request->item_id == 'two-week' || $request->item_id == 'company_two_week') {
            //     $ends_at = $currentDate->addWeeks(2);
        } elseif (Str::contains($inapp->item_id, 'one_month')) {
            $ends_at = $currentDate->addMonths(1);
            $paymentFor = 'subscription';
        } elseif (Str::contains($inapp->item_id, 'three_months')) {
            $ends_at = $currentDate->addMonths(3);
            $paymentFor = 'subscription';
        } elseif (Str::contains($inapp->item_id, 'six_months')) {
            $ends_at = $currentDate->addMonths(6);
            $paymentFor = 'subscription';
        } elseif (Str::contains($inapp->item_id, 'super_chat')) {
            $ends_at = null;
            $paymentFor = 'super_chat';
        }

        $subscriptionType = 'admin-pay';
        $data['user_id'] = $inapp->user_id;
        $data['item_id'] = $inapp->item_id;
        $data['payment_for'] = $paymentFor;
        $data['status'] = 'active';
        $data['ends_at'] = $ends_at ? $ends_at->toDateTimeString() : null;

        $inapp->user->update(['subscriptions_type' => $subscriptionType]);
        $output = InAppPurchase::create($data);
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

        // Optionally, you can return a response to indicate success or failure
        return $this->response()->success('Success message.')->refresh();
    }
}
