<?php

namespace App\Modules\V1\SocialLogin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialLoginEvent
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
