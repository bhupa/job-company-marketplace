<?php

namespace App\Console\Commands;

use App\Models\OtpCheck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UserTruncate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for truncate the user who havent created the profile either company or jobseeker';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        OtpCheck::truncate();

        User::whereDoesntHave('company')
            ->whereDoesntHave('jobseeker')
            ->delete();
    }
}
