<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\InappPurchase\InappPurchaseResourceDetails;
use App\Models\User;
use App\Services\V1\InAppPurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Imdhemy\Purchases\Facades\Product;
use Imdhemy\Purchases\Facades\Subscription;

class GooglePayController extends BaseController
{
    protected $inAppPurchase;

    public function __construct(InAppPurchaseService $inAppPurchase)
    {
        $this->inAppPurchase = $inAppPurchase;
    }

    public function index()
    {
        $user = auth()->user();
        if (env('APP_ENV') == 'production') {
            $data = iapListProd($user);
        } elseif (env('APP_ENV') == 'staging') {
            $data = iapListStagging($user);
        } else {
            $data = iapLists($user);
        }

        return $this->success($data, 'Google pay Plan Lists');
    }

    public function validateGooglePlayReceipt(Request $request)
    {
        if ($request->payment_type == 'google') {
            $productReceipt = Subscription::googlePlay()
                ->id($request->item_id)
                ->token($request->purchase_token)
                ->get();

            return true;
        } else {
            // $subscriptionReceipt = Product::appStore()->receiptData($request->transaction_receipt)->verifyReceipt();
            $receiptResponse = Subscription::appStore()
                ->receiptData($request->transaction_receipt)
                ->verifyReceipt();

            $receipt = $receiptResponse->getReceipt();
            if ($receipt) {
                $inAppList = $receipt->getInApp();

                $productPurchased = false;
                $product = null;
                foreach ($inAppList as $inApp) {
                    if ($inApp->getProductId() === $request->item_id) {
                        $product = $inApp;
                        $productPurchased = true;
                        break;
                    }
                }

                return $productPurchased;
            }

            return false;
        }
    }

    public function paymentDetails(Request $request)
    {
        try {
            $output = $this->inAppPurchase->create($request);
            $user = auth()->user();
            $output['plan'] = getInAppPlan($output, $user);

            return $this->success(
                new InappPurchaseResourceDetails($output),
                'Google pay subscription details'
            );
        } catch (\Throwable $th) {
            return $this->errors(
                ['message' => $th->getMessage(), 'Something went wrong'],
                400
            );
        }
    }

    public function getPlan(Request $request)
    {
        $data = $request->except('_token');
        $user = User::find($data['query']);
        $formattedOutput = [];
        if (env('APP_ENV') == 'dev') {
            if ($user->user_type == 1) {
                $formattedOutput = [
                    'dev_jobseeker_one_week_subscription' => 'One Week',
                    'dev_jobseeker_one_months_subscription' => 'One Month',
                    'dev_jobseeker_three_months_subscription' => 'Three Months',
                    'dev_jobseeker_six_months_subscription' => 'Six Months',
                    'jobseeker_super_chat_dev' => 'Super Chat',
                ];
            } else {
                $formattedOutput = [
                    'dev_company_one_week_subscription' => 'One Week',
                    'dev_company_one_months_subscription' => 'One Month',
                    'dev_company_three_months_subscription' => 'Three Months',
                    'dev_company_six_months_subscription' => 'Six Months',
                    'company_super_chat_dev' => 'Super Chat',
                ];
            }
        } elseif (env('APP_ENV') == 'staging') {
            if ($user->user_type == 1) {
                $formattedOutput = [
                    'staging_jobseeker_one_week_subscription' => 'One Week',
                    'staging_jobseeker_one_months_subscription' => 'One Month',
                    'staging_jobseeker_three_months_subscription' => 'Three Months',
                    'staging_jobseeker_six_months_subscription' => 'Six Months',
                    'jobseeker_super_chat_staging' => 'Super Chat',
                ];
            } else {
                $formattedOutput = [
                    'staging_company_one_week_subscription' => 'One Week',
                    'staging_company_one_months_subscription' => 'One Month',
                    'staging_company_three_months_subscription' => 'Three Months',
                    'staging_company_six_months_subscription' => 'Six Months',
                    'company_super_chat_staging' => 'Super Chat',
                ];
            }
        }

        if ($formattedOutput) {
            $output = array_map(
                function ($key, $value) {
                    return ['id' => $key, 'text' => $value];
                },
                array_keys($formattedOutput),
                $formattedOutput
            );

            return json_encode($output);
        }

        return $formattedOutput;
    }
}
