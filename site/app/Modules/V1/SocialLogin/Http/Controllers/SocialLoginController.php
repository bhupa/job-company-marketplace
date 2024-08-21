<?php

namespace App\Modules\V1\SocialLogin\Http\Controllers;

use App\Http\Controllers\Api\V1\BaseController;
use App\Modules\V1\Company\Http\Resources\CompanyDetailsResource;
use App\Modules\V1\Jobseeker\Http\Resources\JobseekerDetailsResource;
use App\Modules\V1\SocialLogin\Http\Requests\SocialLoginRequest;
use App\Modules\V1\SocialLogin\Traits\AppleLoginTrait;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use App\Modules\V1\User\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends BaseController
{
    // this function is used for the social login
    use AppleLoginTrait;

    public function login(SocialLoginRequest $request)
    {
        try {
            $lang = $request->header('Accept-Language')
                ? $request->header('Accept-Language')
                : 'en';
            $provider = $request->provider;
            $parameters = ['access_type' => 'offline'];
            $username = null;
            $token = $request->token;
            // this is for google
            if ($provider == 'GOOGLE') {
                $providerID = 'google_id';
                $google = Socialite::driver('google')->with($parameters)->scopes(['email', 'phone'])->stateless()->userFromToken($token);
                $email = $google->getEmail();
                $phone = isset($google->user['phone']) ? $google->user['phone'] : null;
                $id = $google->getId();
                $username = ($google->name) ? $google->name : null;
            } elseif ($provider == 'FACEBOOK') {
                // this is for facebook
                $providerID = 'facebook_id';
                $facebook = Socialite::driver('facebook')->with($parameters)->scopes(['email', 'phone', 'public_profile'])->stateless()->userFromToken($token);

                $email = $facebook->getEmail();
                $phone = isset($facebook->user['phone']) ? $facebook->user['phone'] : null;
                $id = $facebook->getId();
                $username = ($facebook->name) ? $facebook->name : null;
            } elseif ($provider == 'APPLE') {
                // this is for apple
                $providerID = 'apple_id';
                $user = $this->$this->appleLogin($request);
                $id = $user->apple_id;
                $username = $request->first_name.' '.$request->last_name;
            }

            $user = User::where($providerID, $id)->first();
            if (empty($user)) {
                $input = [
                    'email' => $email,
                    'status' => 1,
                    'phone' => $phone,
                    'user_type' => $request->user_type,
                ];

                $input[$providerID] = $id;

                $us = User::where('email', $email)->first();

                if ($us) {
                    $us->update([$providerID => $id]);
                    $user = $us;
                } else {
                    $user = User::create($input);
                }
            }

            $use = Auth::login($user);
            $userLang = 'user_lang_'.$user->id;
            Redis::set($userLang, $lang);
            if (auth()->user()->status === 3) {
                $message['message'] = trans(
                    'lang.user.profile_violation',
                    [],
                    $lang
                );

                return $this->errors($message, 403);
            }
            auth()->user()->status == 2
                ? auth()
                    ->user()
                    ->update(['status' => 1])
                : '';
            $data['user'] = new UserDetailsResource(auth()->user());
            $data['provider'] = $provider;
            $data['username'] = $username;
            $data['isSocialSignup'] = true;
            $data['token'] = $user->createToken('JobMacthy')->plainTextToken;
            if (auth()->user()->user_type == 1) {
                $data['jobseeker'] = (auth()->user()->jobseeker) ? new JobseekerDetailsResource(auth()->user()->jobseeker) : null;
            } else {
                $data['company'] = auth()->user()->company ? new CompanyDetailsResource(auth()->user()->company) : null;
            }

            return $this->success($data, 'Login Successful');
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }
}
