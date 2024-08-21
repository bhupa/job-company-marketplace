<?php

namespace App\Modules\V1\User\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct()
    {
    }
}
