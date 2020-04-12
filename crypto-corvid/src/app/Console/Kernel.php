<?php

namespace App\Console;

use App\Models\Constants\TaskConstants;
use App\Models\Pg\Task;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        $tasks = Task::getEnabled();
        
        foreach ($tasks as $task) {
            $taskName = $task->getAttribute(Task::COL_NAME);
            $taskFreq = $task->getAttribute(Task::COL_FREQ);
            $taskStart = $task->getAttribute(Task::COL_STARTING);
            $command = 'docker-compose -f ../docker/dev/infra.yml -f ../docker/dev/backend.yml run --rm scraper '. $taskName . ' 2';

            $preTask = $schedule->exec($command)->runInBackground()->storeOutput();
            
            switch ($taskFreq) {
                case TaskConstants::MINUTE:
                    $preTask->everyMinute();
                    break;
                case TaskConstants::HOURLY:
                    $hour = preg_split('/:/', $taskStart)[0];
                    $preTask->hourlyAt($hour);
                    break;
                case TaskConstants::DAILY:
                    $preTask->dailyAt($taskStart);
                    break;
                case TaskConstants::WEEKLY:
                    $preTask->weeklyOn(1, $taskStart);
                    break;
            }
        }
         
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
