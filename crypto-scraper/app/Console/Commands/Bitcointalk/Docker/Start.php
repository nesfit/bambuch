<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Docker;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use Symfony\Component\Process\Process;

//docker-compose -f common.yml -f dev.yml run --rm --name bct_main_boards_producer test btc:main_boards_producer 2

class Start extends BitcointalkParser {
    const COMMON_ARGS = ["docker-compose", "-f", "common.yml", "-f", "dev.yml", "run"];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::START;

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
//        $this->startModule(self::MAIN_BOARDS_PRODUCER);
//        $this->startModule(self::BOARD_PAGES_PRODUCER);
//        $this->startModule(self::MAIN_TOPICS_PRODUCER);
//        $this->startModule(self::TOPIC_PAGES_PRODUCER);
//        $this->startModule(self::TOPIC_PAGES_CONSUMER);
        $this->startModule(self::SCRAPED_RESULTS_CONSUMER);
        
        print "Ending... \n";
        return 0;
    }
    
    private function startModule(string $module) {
        $dockerName = str_replace(":", "_", $module);
        print "Starting module: " . $dockerName . "\n";
        $process = new Process(array_merge(self::COMMON_ARGS, ["--name", $dockerName, "test", $module, "2"]));
        $process->start();
        
        sleep(5);
    }
}
