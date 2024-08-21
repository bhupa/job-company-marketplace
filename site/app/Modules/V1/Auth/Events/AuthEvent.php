<?php

namespace App\Modules\V1\Auth\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuthEvent
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
