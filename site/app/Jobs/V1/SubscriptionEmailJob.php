<?php

namespace App\Jobs\V1;

use App\Mail\V1\SubscriptionEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SubscriptionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subscription;
    protected $plan;
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($subscription, $plan, $user)
    {
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(
            new SubscriptionEmail($this->subscription, $this->plan, $this->user)
        );
    }
}
