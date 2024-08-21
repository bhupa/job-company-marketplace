<?php

namespace App\Services\V1;

use App\Events\V1\ChatRefreshEvent;
use App\Models\ChatRoom;
use App\Models\Payment;
use App\Models\User;
use App\Models\V1\Esewa;
use App\Models\V1\ForceUpdate;
use App\Services\BaseService;
use App\Services\FireBaseService;
use App\Traits\SubscriptionsTrait;
use Illuminate\Support\Facades\Http;

class EsewaService extends BaseService
{
    use SubscriptionsTrait;
    protected $esewa;
    protected $fireBaseService;

    public function __construct(Esewa $esewa, FireBaseService $fireBaseService)
    {
        $this->model = $esewa;
        $this->fireBaseService = $fireBaseService;
    }

    public function create($request, $userId, $type, $priceId, $roomId)
    {
        $user = User::find($userId);
        $output = $this->store($request, $userId, $type, $priceId);
        if ($type === 'subscription') {
            if ($request->platform == 'ios' || $request->platform == 'android') {
                $inapp = new \stdClass();
                $inapp->item_id = $priceId;
                $inapp->payment_type = ($request->platform == 'ios') ? 'apple' : 'google';
                $price = getInAppPlan($inapp, $user);
                $plan = new \stdClass();
                $plan->unit_label = $price['timePeriod']['en'];
                $endDates = $this->getSubscriptionEndDates($plan);
                } elseif ($request->platform == 'web') {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $price = Price::retrieve($priceId);
                $plan = Product::retrieve($price->product);
                $endDates = $this->getSubscriptionEndDates($plan);
                }

            subscriptionAction($userId);
            $user->update([
                'subscriptions_type' => 'esewa',
            ]);
            $output->update(['ends_at' => $endDates]);
        } else {
            $this->unrestrictedChat($output, $roomId, $user);
        }

        return $output;
        //    Array ( [transaction_code] => 0007GME [status] => COMPLETE [total_amount] => 200.0 [transaction_uuid] => unrestrictedChat-53-240510-15514 [product_code] => EPAYTEST [signed_field_names] => transaction_code,status,total_amount,transaction_uuid,product_code,signed_field_names [signature] => 6h7D8zX+MZVNxwCsQyadpL4hXDn2HLkXCef+W8z+0Y8= )
    }

    public function store($request, $userId, $type, $priceId)
    {
        $decoded_data = $this->decodeEsewaResponse($request);
        //   dd($decoded_data);
        $input['user_id'] = $userId;
        $input['type'] = ($type == 'unrestricted-chat') ? 'super_chat' : $type;
        $input['price_id'] = $priceId;
        $input['payment_form'] = $request->platform;
        $input['transaction_code'] = $decoded_data['transaction_code'];
        $input['status'] = ($decoded_data['status'] === 'COMPLETE') ? 'active' : 'pending';
        $input['transaction_uuid'] = $decoded_data['transaction_uuid'];
        $input['product_code'] = $decoded_data['product_code'];
        $input['signature'] = $decoded_data['signature'];

        return $this->model->create($input);
    }

    public function unrestrictedChat($output, $roomId, $user)
    {
        $chatroom = ChatRoom::find($roomId);
        $chatOutput = [
            'type' => 'esewa',
            'payment_intent_id' => $output->id,
            'status' => 1,
            'user_id' => $user->id,
            'model_id' => $roomId,
            'model' => 'App\Models\ChatRoom',
        ];
        $payment = Payment::create($chatOutput);
        $chatroom->update([
            'payment_id' => $payment->id,
            'payment_type' => 'esewa',
        ]);
        $user->device_token
            && $this->fireBaseService->sendOtp(
                $user,
                'unrestricted-chat',
                $chatroom
            );

        $user
            && broadcast(
                new ChatRefreshEvent(['user_id' => $user->id])
            );

        return $payment;
    }

    public function decodeEsewaResponse($request)
    {
        $data = $request->get('?data');
        $decoded_data = base64_decode($data);

        return json_decode($decoded_data, true);
    }

    public function verifyTransaction($request)
    {
        $decoded_data = $this->decodeEsewaResponse($request);
        
        $url =  (env('APP_ENV') === 'production') ? env('ESEWA_PRODUCTION_URL') : env('ESEWA_TEST_URL') ;
      
        // Remove comma from total amount value
        $total_amount = str_replace( ',', '', $decoded_data['total_amount']);

        $fullUrlparms = $url.'?product_code='.$decoded_data['product_code'].'&total_amount='.$total_amount.'&transaction_uuid='.$decoded_data['transaction_uuid'];

        // Make the GET request to the API
        $response = Http::get($fullUrlparms);

        return $response->json();
    }
}
