<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use Symfony\Component\Process\Process;

class Startup extends BitcointalkParser {
    const COMMON_ARGS = ["docker-compose", "-f", "common.yml", "-f", "dev.yml", "-f", "graylog.yml", "run"];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::STARTUP;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run bitcointalk containers';
    
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
        $this->startModule(self::MAIN_BOARDS_PRODUCER);
        $this->startModule(self::MAIN_BOARDS_KEEPER);
        $this->startModule(self::BOARD_PAGES_PRODUCER);
        $this->startModule(self::BOARD_PAGES_KEEPER);
        
        
        print "Ending... \n";
        return 0;
    }
    
    private function startModule(string $module) {
        $process = new Process(array_merge(self::COMMON_ARGS, ["--rm", "test", $module, "2"]));
        $process->start();
        
        sleep(2);
    }
}
