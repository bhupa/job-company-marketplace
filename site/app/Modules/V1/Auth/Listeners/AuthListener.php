<?php

namespace App\Modules\V1\Auth\Listeners;

use App\Modules\V1\Auth\Events\AuthListener;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AuthListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param \App\Modules\V1\Auth\Events\AuthListener $event
     *
     * @return void
     */
    public function handle(AuthListener $event)
    {
    }
}
