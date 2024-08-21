<?php

namespace App\Modules\V1\Auth\Http\Controllers;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Modules\V1\Auth\Http\Requests\LoginRequestRequest;
use App\Modules\V1\Auth\Http\Requests\RegisterRequest;
use App\Modules\V1\Company\Http\Resources\CompanyDetailsResource;
use App\Modules\V1\Jobseeker\Http\Resources\JobseekerDetailsResource;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use App\Modules\V1\User\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AuthController extends BaseController
{
    protected $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    // Define your controller methods here

    public function login(LoginRequestRequest $request)
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
            $data['user'] = new UserDetailsResource(auth()->user());
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

            event(new Registered($user));

            // this is used to send the email after succcess registration
            return $this->success(
                [
                    'user' => new UserDetailsResource($user),
                    'provider' => null,
                    'token' => $user->createToken('JobMacthy'.$user->email)
                        ->plainTextToken,
                ],
                trans('lang.user.register_successfully', [], $lang)
            );
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            return $this->errors(
                [
                    'message' => $msg ?? trans(
                        'lang.errors.invalid_credentials',
                        [],
                        $lang
                    ),
                ],
                400
            );
        }
    }

    public function logout(Request $request)
    {
        // $request->user()->tokens()->delete();
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        auth()
            ->user()
            ->update(['device_token' => null]);
        \Auth::guard('web')->logout();

        return $this->success([], trans('user.logout_successfully', [], $lang));
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
