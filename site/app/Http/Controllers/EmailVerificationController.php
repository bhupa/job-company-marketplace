<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verifyEmail(Request $request)
    {
        $id = $request->segment(3);
        $user = User::find($id);
        if ($user->hasVerifiedEmail()) {
            return redirect()->to('/login');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->to('/login?verified=true');
    }
}
