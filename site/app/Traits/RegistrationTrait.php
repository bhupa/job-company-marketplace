<?php

namespace App\Traits;

use Carbon\Carbon;

trait RegistrationTrait
{
    public function addTrialAccount($user)
    {
        $today = Carbon::now();

        if (
            !app()->environment('production')
            && $today->lte(Carbon::parse(env('TRIAL_END_DATE_DEV')))
        ) {
            return getTrialSubscription($user);
        }
        // Check if it's within the trial period in production environment
        if (
            app()->environment('production')
            && $today->lte(Carbon::parse(env('TRIAL_END_DATE_PROD')))
        ) {
            return getTrialSubscription($user);
        }
    }
}
