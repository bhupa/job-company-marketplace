<?php

namespace App\Modules\V1\Auth\Http\Controllers;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Controllers\Controller;
use App\Modules\V1\Auth\Http\Requests\ChangeEmailRequest;
use App\Modules\V1\User\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Verified;

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

    // Define your controller methods here
}
