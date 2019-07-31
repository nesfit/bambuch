<?php
/**
 * Copyright (c) 2017.
 * This file is part of COINOMON which is released under private license.
 * Go to http://netsearch.cz/coinomon/license for full license details.
 *
 * You are not authorized to modify, disassemble or sell any part of COINOMON source codes.
 * You are not authorized to use COINOMON without valid service contract.
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DownloadIdentities::class,
        Commands\Bitcointalk::class,
        Commands\BitcointalkImport::class,
        Commands\ClusterRefresh::class,
        Commands\Bitcoinabuse::class,
        Commands\BitcoinabuseImport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cluster:refresh')->dailyAt('04:00');
        $schedule->command('cluster:refresh')->dailyAt('07:00');
        $schedule->command('cluster:refresh')->dailyAt('12:00');
        $schedule->command('cluster:refresh')->dailyAt('19:00');
        $schedule->command('cluster:refresh')->dailyAt('23:00');
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
