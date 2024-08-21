<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\V1\ChatUnseenCountEvent;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Requests\V1\DeviceToken\DeviceTokenRequest;
use App\Http\Requests\V1\Phone\CheckPhoneRequest;
use App\Http\Requests\V1\UserChangeStatusRequest;
use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use App\Models\OtpCheck;
use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    protected $userservice;
    protected $subscriptionService;

    public function __construct(
        UserService $userservice,
        SubscriptionService $subscriptionService
    ) {
        $this->userservice = $userservice;
        $this->subscriptionService = $subscriptionService;
    }

    // this function is used for the login
    public function login(LoginRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        $condition = $request->has('email')
            ? \Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])
            : \Auth::attempt([
                'phone' => $request->phone,
                'password' => $request->password,
            ]);
        if ($condition) {
            $data['user'] = new UserDetailsResources(auth()->user());
            $data['provider'] = null;
            $data['isSocialSignup'] = false;
            $data['token'] = auth()
                ->user()
                ->createToken('JobMacthy')->plainTextToken;
            if (auth()->user()->user_type == 1) {
                $data['jobseeker'] = auth()->user()->jobseeker
                    ? new JobseekerDetailsResource(auth()->user()->jobseeker)
                    : null;
            } else {
                $data['company'] = auth()->user()->company
                    ? new CompanyDetailsResource(auth()->user()->company)
                    : null;
            }
            auth()->user()->status == 2
                ? auth()
                    ->user()
                    ->update(['status' => 1])
                : '';

            $data['isSubscribed'] =
                getSubscribedPlan() !== null
                    ? getSubscribedPlan()['isSubscribed']
                    : false;
            $data['subscription'] =
                getSubscribedPlan() !== null
                    ? getSubscribedPlan()['subscription']
                    : null;
            if (auth()->user()->status === 3) {
                $message['message'] = trans(
                    'lang.user.profile_violation',
                    [],
                    $lang
                );

                return $this->errors($message, 403);
            }

            return $this->success(
                $data,
                trans('lang.user.login_successful', [], $lang)
            );
        }
        $message['message'] = trans(
            'lang.errors.invalid_credentials',
            [],
            $lang
        );

        return $this->errors($message, 400);
    }

    // this function is used for user registration
    public function register(RegisterRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        $data = $request->except('_token', 'password_confirmation');
        $data['password'] = \Hash::make($request->password);
        $data['status'] = 1;
        try {
            $user = $this->userservice->create($data);
            $this->userservice->checkOtpCount($request);
            $user->sendEmailVerificationNotification();
            $userLang = 'user_lang_'.$user->id;
            Redis::set($userLang, $lang);

            // event(new Registered($user));
            // this is used to send the email after succcess registration
            return $this->success(
                [
                    'user' => new UserDetailsResources($user),
                    'provider' => null,
                    'token' => $user->createToken('JobMacthy'.$user->email)
                        ->plainTextToken,
                ],
                trans('lang.user.register_successfully', [], $lang)
            );
        } catch (\Exception $e) {
            return $this->errors(
                [
                    'message' => trans(
                        'lang.errors.invalid_credentials',
                        [],
                        $lang
                    ),
                ],
                400
            );
        }
    }

    // this function  is used for logout
    public function logout(Request $request)
    {
        // $request->user()->tokens()->delete();
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        auth()
            ->user()
            ->update(['device_token' => null]);
        Auth::guard('web')->logout();

        return $this->success([], trans('user.logout_successfully', [], $lang));
    }

    // this function is used for thechange user status
    public function changeStatus(UserChangeStatusRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        try {
            $user = $this->userservice->changeStatus($request->status);

            return $this->success(
                [
                    'user' => new UserDetailsResources($user),
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
                        'user' => new UserDetailsResources($user),
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
                    'user' => new UserDetailsResources($user),
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
                    'user' => new UserDetailsResources(
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
