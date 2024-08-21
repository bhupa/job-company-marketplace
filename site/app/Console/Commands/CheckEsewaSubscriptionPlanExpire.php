<?php

namespace App\Console\Commands;

use App\Models\V1\Esewa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckEsewaSubscriptionPlanExpire extends Command
    {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-esewa-subscription-plan-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
        {
        $today = now();
        // Retrieve active subscriptions ending one day prior to today
        $subscriptions = Esewa::where('type', 'subscription')
            ->where('status', 'active')
            ->whereDate('ends_at', $today->subDays(1)->toDateString())
            ->get();
        // Update the status of subscriptions to 'expired'
        foreach ($subscriptions as $subscription) {
            $subscription->update(['status' => 'expired']);
            }
        Log::info('esewa cron job is running ' . $today);
        $this->info('Expired subscriptions updated successfully.');
        }
    }
