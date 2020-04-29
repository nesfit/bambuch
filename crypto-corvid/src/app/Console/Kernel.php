<?php
declare(strict_types=1);

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

    private function getComposeFile(string $container): string {
        if(preg_match('/bct_all/', $container)) return '../docker/dev/bitcointalk-reproducers.yml';
        if(preg_match('/bca_/', $container)) return '../docker/dev/bitcoinabuse-base.yml';
        if(preg_match('/bct_/', $container)) return '../docker/dev/bitcointalk-base.yml';
        return 'unknown-compose.yml';
    }
    
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
            
            $container = preg_replace('/:/', '_', $taskName);
            $composeFile = $this->getComposeFile($container);
            $command = 'docker-compose -f ../docker/dev/infra.yml -f '. $composeFile . ' up -d '. $container;
            
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
