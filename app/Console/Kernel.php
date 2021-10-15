<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ImportOldData_Operators::class,
        \App\Console\Commands\ImportOldData_Prefectures::class,
        \App\Console\Commands\ImportOldData_Lines::class,
        \App\Console\Commands\ImportOldData_Stations::class,
        \App\Console\Commands\ImportOldData_Trains::class,
        \App\Console\Commands\ImportOldData_VehiclePerformances::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
