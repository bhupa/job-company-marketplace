<?php

namespace App\Jobs;

use App\Mail\SubscriptionExpiredEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscriptionExpiredNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $subscription;
    protected $plan;
    protected $price;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($subscription, $plan, $price, $user)
    {
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->price = $price;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Mail::to($this->user->email)->send(new SubscriptionExpiredEmail($this->plan, $this->user));
    }
}
