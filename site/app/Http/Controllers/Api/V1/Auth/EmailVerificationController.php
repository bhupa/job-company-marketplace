<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\V1\Auth\ChangeEmailRequest;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailVerificationController extends BaseController
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => trans(
                    'lang.user.email.verified',
                    [],
                    getUserLanguage($request->user())
                ),
            ];
        }

        // Send the email verification notification with the verification URL
        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->success(
                [],
                trans(
                    'lang.user.email.verified',
                    [],
                    getUserLanguage($request->user())
                )
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->success(
            [],
            trans(
                'lang.user.email.verify',
                [],
                getUserLanguage($request->user())
            )
        );
    }

    public function updateEmail(ChangeEmailRequest $request)
    {
        try {
            $user = User::find(auth()->id());
            tap(
                $user->update([
                    'email' => $request->email,
                    'email_verified_at' => null,
                    'verification_token' => Str::random(40),
                ])
            );
            $currentAccessToken = $user->currentAccessToken();
            if ($currentAccessToken) {
                $currentAccessToken->delete();
            }
            $user->sendEmailVerificationNotification();

            return $this->success(
                [],
                trans(
                    'lang.user.email.changed',
                    [],
                    getUserLanguage($request->user())
                )
            );
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }
}
