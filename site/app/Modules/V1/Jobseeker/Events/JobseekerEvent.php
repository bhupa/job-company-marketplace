<?php

namespace App\Modules\V1\Jobseeker\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobseekerEvent
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
