<?php

namespace App\Modules\V1\User\Http\Controllers;

use App\Events\V1\ChatUnseenCountEvent;
use App\Http\Controllers\Controller;
use App\Models\OtpCheck;
use App\Modules\V1\User\Http\Requests\CheckPhoneRequest;
use App\Modules\V1\User\Http\Requests\DeviceTokenRequest;
use App\Modules\V1\User\Http\Requests\UserChangeStatusRequest;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use App\Modules\V1\User\Models\User;
use App\Modules\V1\User\Services\UserService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userservice;
    protected $subscriptionService;

    public function __construct(
        UserService $userservice,
        SubscriptionService $subscriptionService
    ) {
        $this->userservice = $userservice;
        $this->subscriptionService = $subscriptionService;
    }

    // this function  is used for logout
    public function changeStatus(UserChangeStatusRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        try {
            $user = $this->userservice->changeStatus($request->status);

            return $this->success(
                [
                    'user' => new UserDetailsResource($user),
                ],
                trans('user.status_update', [], $lang)
            );
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => trans('errors.invalid_credentials', [], $lang)],
                400
            );
        }
    }

    // this function is used to delete user
    public function destroy(User $user)
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';
        try {
            if ($user) {
                $this->userservice->deleteUserAccount($user);

                return $this->success(
                    [
                        'user' => new UserDetailsResource($user),
                    ],
                    trans('user.detail', [], $lang)
                );
            }
        } catch (\Exception $e) {
            return $this->errors(
                ['message' => trans('errors.invalid_credentials', [], $lang)],
                400
            );
        }
    }

    public function getUserDetails($id)
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';
        $user = $this->userservice->getDetails($id);
        if ($user) {
            return $this->success(
                [
                    'user' => new UserDetailsResource($user),
                ],
                trans('user.detail', [], $lang)
            );
        }

        return $this->errors(
            ['message' => trans('not_found', ['name' => 'User'], $lang)],
            400
        );
    }

    public function addingDeviceToke(DeviceTokenRequest $request)
    {
        try {
            $user = auth()->user();
            $user->update(['device_token' => $request->device_token]);
            $this->userservice->deleteDeviceToken($request->device_token);
            $totalUnseen = unseenCount($user);
            $unseen = [
                'receiveBy' => $user->id,
                'unseenCount' => $totalUnseen,
            ];
            // Redis::publish('chat', json_encode($data));
            broadcast(new ChatUnseenCountEvent($unseen));

            return $this->success(
                [
                    'user' => new UserDetailsResource(
                        auth()->user()->refresh()
                    ),
                ],
                trans('user.device_token_refresh', [], getUserLanguage($user))
            );
        } catch (\Exception $e) {
            return $this->errors(
                [
                    'message' => trans(
                        'errors.invalid_credentials',
                        [],
                        getUserLanguage($user)
                    ),
                ],
                400
            );
        }
    }

    public function checkPhone(CheckPhoneRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        $phone = $this->userservice->checkPhone($request);

        if ($request->has('phone') && empty($phone)) {
            $token = $this->userservice->checkOtpCount($request);
            if ($token && $token->count() >= 3) {
                return $this->errors(
                    ['message' => trans('lang.user.max_otp', [], $lang)],
                    400
                );
            }
        }

        if ($phone) {
            $message = $request->has('phone')
                ? __('validation.unique.phone', [], $lang)
                : __('validation.unique.email', [], $lang);

            return $this->errors(
                [
                    'message' => $message,
                ],
                400
            );
        }
        $message = $request->has('phone')
            ? __('validation.exists.phone', [], $lang)
            : __('validation.exists.email', [], $lang);

        return $this->success([], $message);
    }

    public function otpCount(Request $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        $token = $this->userservice->checkOtpCount($request);

        if ($token->count() <= 2) {
            $devicetoken = OtpCheck::where(
                'device_token',
                $request->header('DeviceID')
            )
                ->whereDate('created_at', '=', Carbon::now()->toDateString())
                ->get();

            return $this->success(
                ['otp_count' => $devicetoken],
                trans('user.total_otp', [], $lang)
            );
        }

        return $this->errors(
            ['message' => trans('user.total_max_opt', [], $lang)],
            400
        );
    }

    public function refreshToken(Request $request)
    {
        if (auth()->user()) {
            auth()->user()->tokens()->delete();

            return $this->success(
                [
                    'token' => auth()->user()->createToken('JobMacthy')
                        ->plainTextToken,
                ],
                'User token refreshed'
            );
        }
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';

        return $this->errors(
            ['message' => trans('not_found', ['name' => 'Token'], $lang)],
            403
        );
    }
}
