<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class KafkaStop extends Command {
    const COMMON_ARGS = ["docker-compose", "-f", "common.yml", "stop"];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop kafka';
    
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
        $this->startModule("kafka");
        $this->startModule("zookeeper");
        return 0;
    }

    private function startModule(string $module) {
        print "Stopping: " . $module . "\n";
        $process = new Process(array_merge(self::COMMON_ARGS, [$module]));
        $process->start();

        sleep(2);
    }
}
