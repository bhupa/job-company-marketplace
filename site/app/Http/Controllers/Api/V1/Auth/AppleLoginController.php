<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as OAuthTwoUser;

class AppleLoginController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public static function appleLogin(Request $request)
    {
        $provider = 'apple';
        $token = $request->token;
        $lang = $request->header('Accept-Language')
            ? $request->header('Accept-Language')
            : 'en';
        $socialUser = Socialite::driver($provider)->userFromToken($token);

        $socialUser['user_type'] = $request->user_type;
        $user = self::getLocalUser($socialUser, $lang);

        $client = DB::table('oauth_clients')
            ->where('user_id', $user->id)
            ->where('password_client', true)
            ->first();

        if (!$client) {
            $insertedClientId = DB::table('oauth_clients')->insertGetId([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $socialUser->id, // Assuming getId() returns the provider user ID
                // Add any additional fields you need for the OAuth client
                'secret' => env('APPLE_CLIENT_SECRET'),
                'password_client' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Retrieve the inserted OAuth client record
            $client = DB::table('oauth_clients')->find($insertedClientId);
        }

        return $user;
    }

    public static function getLocalUser(OAuthTwoUser $socialUser, $lang): ?User
    {
        $user = User::where('email', $socialUser->email)->first();
        if (!$user) {
            $user = self::registerAppleUser($socialUser, $lang);
        }
        $user->update(['apple_id' => $socialUser->id]);

        return $user;
    }

    public static function registerAppleUser(OAuthTwoUser $socialUser, $lang): ?User
    {
        $user = User::create([
            // 'full_name' => request()->fullName ? request()->fullName : 'Apple User',
            'email' => $socialUser->email,
            'apple_id' => $socialUser->id,
            'user_type' => $socialUser['user_type'],
            'status' => 1,
            // 'password' => Str::random(30), // Social users are password-less
        ]);
        $userLang = 'user_lang_'.$user->id;
        Redis::set($userLang, $lang);

        return $user;
    }
}
