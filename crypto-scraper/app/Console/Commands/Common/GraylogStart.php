<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class GraylogStart extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'graylog:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run graylog';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $process = new Process(["docker-compose", "-f", "graylog.yml", "up", "-d", "graylog"]);
        $process->start();

        sleep(3);
        
        return 0;
    }
}
