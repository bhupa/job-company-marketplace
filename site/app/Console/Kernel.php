<?php

namespace App\Console;

use App\Console\Commands\MakeModuleController;
use App\Console\Commands\MakeModuleEvent;
use App\Console\Commands\MakeModuleFileCommand;
use App\Console\Commands\MakeModuleMail;
use App\Console\Commands\MakeModuleModel;
use App\Console\Commands\MakeModuleNotification;
use App\Console\Commands\MakeModuleRequest;
use App\Console\Commands\MakeModuleResource;
use App\Console\Commands\MakeModuleService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */



    protected $commands = [
        MakeModuleController::class,
        MakeModuleEvent::class,
        MakeModuleMail::class,
        MakeModuleModel::class,
        MakeModuleNotification::class,
        MakeModuleRequest::class,
        MakeModuleResource::class,
        MakeModuleService::class
       
       
       
    ];     protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('app:check-subscrutpion-plan-expire')->hourly();
        $schedule
            ->command('app:check-subscrutpion-plan-expire')
            ->daily();
        $schedule->command('app:check-esewa-subscription-plan-expire')->daily();
        $schedule->command('app:user-truncate')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
