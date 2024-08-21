<?php

namespace App\Modules\V1\Company\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyEvent
{
    use Dispatchable, SerializesModels;

    public function __construct()
    {
        //
    }
}
