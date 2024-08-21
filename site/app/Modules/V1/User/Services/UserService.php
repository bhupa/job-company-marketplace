<?php

namespace App\Modules\V1\User\Services;

use App\Mail\V1\AccountDeleteMail;
use App\Models\OtpCheck;
use App\Modules\V1\User\Models\User;
use App\Services\BaseService;
use App\Services\ImageFileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    protected $image;

    public function __construct(User $user, ImageFileService $image)
    {
        $this->model = $user;
        $this->image = $image;
    }

    // this function is used to create user
    public function create($data)
    {
        $data['verification_token'] = Str::random(60);

        return $this->model->create($data);
    }

    // this function is used to  change the status of the user
    public function changeStatus($value)
    {
        tap(
            $this->model->findOrFail(auth()->id())->update(['status' => $value])
        );

        return $this->model->find(auth()->id());
    }

    // this function is used reset the password
    public function resetPassword($request)
    {
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $user = User::find($request->user_id);
        }

        tap(
            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
            ])
        );

        return $user;
    }

    public function checkPhone($request)
    {
        if ($request->has('phone')) {
            return $this->model
                ->where('phone', $request->phone)
                ->where('country_code', $request->country_code)
                ->first();
        } else {
            return $this->model->where('email', $request->email)->first();
        }
    }

    public function checkOtpCount($request)
    {
        $deviceToken = $request->header('DeviceID');
        if ($deviceToken) {
            $token = OtpCheck::where('device_token', $deviceToken)
                ->whereDate('created_at', '=', Carbon::now()->toDateString())
                ->get();

            if ($token->count() <= 2) {
                OtpCheck::create(['device_token' => $deviceToken]);
            }

            return $token;
        }

        return null;
    }

    public function verifyUserAccount($request)
    {
        $files = $request->images;
        $images = $this->image->create(
            auth()->id(),
            $files,
            'App\Models\User',
            'user-verify'
        );

        return $images;
    }

    public function profilePercentage()
    {
        if (auth()->user()->user_type == 1) {
            $userInput = [
                'current_country',
                'occupation',
                'experience',
                'job_type',
            ];

            $verifed = auth()->user()->jobseeker->is_verify ? 30 : 0;
            $i = 0;
            foreach ($userInput as $input) {
                if (auth()->user()->jobseeker[$input]) {
                    ++$i;
                }
            }
            $validation = 30;
            $totalPercentage = $validation + $verifed + ($i / 4) * 40;
        } else {
            $verifed = auth()->user()->company->is_verify ? 30 : 0;
            $image = auth()->user()->company->images ? 10 : 0;
            $logo = auth()->user()->company->logo ? 10 : 0;
            $validation = 50;
            $totalPercentage = $validation + $verifed + $image + $logo;
        }

        return $totalPercentage;
    }

    public function violationUser($user)
    {
        $userType = $user->user_type;
        $this->model->where('id', $user->id)->update(['status' => 3, 'device_token' => null]);
        $user->tokens() && $user->tokens()->delete();

        return true;
    }

    public function deleteDeviceToken($deveiceToken)
    {
        return $this->model
            ->where('device_token', $deveiceToken)
            ->where('id', '!=', auth()->id())
            ->update(['device_token' => null]);
    }

    public function deleteUserAccount($user)
    {
        if ($user->user_type == 1) {
            if ($user->jobseeker) {
                $user->jobseeker->relationLoaded('matching')
                    && $user->jobseeker->matching->isNotEmpty()
                    ? $user->jobseeker->matching->delete()
                    : null;
                $user->jobseeker->delete();
                Log::info('user jobseeker delete');
            }
        } else {
            if ($user->jobs->isNotEmpty()) {
                $user->jobs()->delete();
            }

            $user->company->relationLoaded('matching')
                && $user->company->matching->isNotEmpty()
                ? $user->company->matching->delete()
                : null;
            $user->company->delete();
        }
        if ($user->subscribed('default')) {
            ($user->subscription('default')->stripe_status != 'cancel') && $user->subscription('default')->cancel();
        }
        $user->relationLoaded('matching') && $user->matching->isNotEmpty()
            ? $user->matching->delete()
            : null;
        if ($user->subscriptions_type == 'iap' || $user->subscriptions_type == 'google') {
            Mail::to($user->email)->send(
                new AccountDeleteMail($user)
            );
        }
        Log::info('user matching is  delete');

        return $user->delete();
    }
}
