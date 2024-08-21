<?php

namespace App\Modules\V1\Auth\Http\Controllers;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Modules\V1\Auth\Http\Requests\ChangePasswordRequest;
use App\Modules\V1\Auth\Http\Requests\ForgetPassswordRequest;
use App\Modules\V1\Auth\Http\Requests\ResetPasswordRequest;
use App\Modules\V1\Auth\Http\Requests\VerifyOtpRequest;
use App\Modules\V1\Auth\Notifications\ResetPasswordNotification;
use App\Modules\V1\User\Models\User;
use App\Modules\V1\User\Services\UserService;
use Illuminate\Http\Request;

class EmailForgotPasswordController extends BaseController
{
    public $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }

    // this function is used to send the otp for forget password
    public function forgot(ForgetPassswordRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        // this function will give the otp toke
        $otp = $this->generateOtp();

        try {
            if ($request->has('email')) {
                $user = User::where('email', $request->email)->first();
                $user->notify(new ResetPasswordNotification($otp));
                tap($user->update(['otp' => $otp]));
                $message = trans('lang.user.check_email', [], $lang);
            } else {
                $user = User::where('phone', $request->phone)->first();
                $message = trans(
                    'lang.exists.already',
                    ['name' => 'phone'],
                    $lang
                );
                $token = $this->userservice->checkOtpCount($request);
                if ($token->count() >= 3) {
                    return $this->errors(
                        [
                            'message' => trans('lang.user.max_otp', [], $lang),
                        ],
                        400
                    );
                }
            }

            $data = [
                'email' => $user->email,
                'phone' => $user->phone,
                'countryCode' => $user->country_code,
                'userId' => $user->id,
            ];

            return $this->success($data, $message);
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    // this function is used for changing
    public function reset(ResetPasswordRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        try {
            $this->userservice->resetPassword($request);

            return $this->success(
                [],
                trans('lang.user.password.reset', [], $lang)
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }

        return $this->errors(
            ['message' => trans('lang.invalid_otp', [], $lang)],
            400
        );
    }

    // this function will check if opt
    public function checkUniqueOtp($otp)
    {
        return User::where('otp', $otp)->whereNotNull('otp')->first();
    }

    // this function will generate the otp
    public function generateOtp()
    {
        $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        if ($this->checkUniqueOtp($otp)) {
            return $this->generateOtp();
        }

        return $otp;
    }

    // this function is used for verifying the otp for forget password
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        try {
            $user = $this->checkUniqueOtp($request->otp);
            if($user) {
                $data = [
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'countryCode' => $user->country_code,
                    'userId' => $user->id,
                ];
                return $this->success($data, trans('lang.user.details', [], $lang));
            }

            return $this->errors(trans('lang.invalid_otp'), 400);


        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        try {
            $this->userservice->resetPassword($request);

            return $this->success(
                [],
                trans('lang.user.password.changed', [], $lang)
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }

        return $this->errors(
            ['message' => trans('lang.invalid_otp', [], $lang)],
            400
        );
    }

    // Define your controller methods here
}
