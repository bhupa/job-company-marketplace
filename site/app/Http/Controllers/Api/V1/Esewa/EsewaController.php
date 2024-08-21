<?php

namespace App\Http\Controllers\Api\V1\Esewa;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\V1\EsewaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Price;
use Stripe\Stripe;

class EsewaController extends Controller
{
    protected $esewaService;
    public function __construct(EsewaService $esewaService){
       $this->esewaService = $esewaService;
    }
    public function index(Request $request)
    { 
        try {
        // Fetch necessary data from the request
        $userId = $request->get('user_id');
        $priceId = $request->get('price_id');
        $type = $request->get('type');
        $platform = $request->get('platform');

        // Retrieve the user
        $user = User::find($userId);

        // Verify the transaction with eSewa
        $transactionStatus = $this->esewaService->verifyTransaction($request);

           
        // Check if the transaction status is 'COMPLETE'
        if ($transactionStatus['status'] !== 'COMPLETE') {
                return redirect()->to('/');
        }

        // Determine the price based on the platform and type
        $price = null;

        if (($platform == 'ios' || $platform == 'android') && $type == 'subscription') {
            $inapp = new \stdClass();
            $inapp->item_id = $priceId;
            $inapp->payment_type = ($platform == 'ios') ? 'apple' : 'google';
            $price = getInAppPlan($inapp, $user);
            
        } elseif ($platform == 'web') {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $price = Price::retrieve($priceId);
        }

        // Get room ID if provided
        $roomId = $request->has('room_id') ? $request->get('room_id') : null;
       
        // Create the eSewa record
        $this->esewaService->create($request, $userId, $type, $priceId, $roomId);

        // Determine the URL to redirect to
        $url = $request->has('url_value') 
            ? $request->get('url') . '/' . $request->get('url_value') 
            : $request->get('url');

        // Redirect to the URL
        return redirect()->to($url.'&status=success');

        } catch (\Exception $e) {
            Log::error('Error in transaction processing: ' . $e->getMessage());
            return redirect()->to('/home?status=failure');
        }
    }
  
   
}
