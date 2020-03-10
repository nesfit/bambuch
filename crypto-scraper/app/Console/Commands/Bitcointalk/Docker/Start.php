<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Docker;

use App\Console\Constants\BitcointalkCommands;
use App\Console\Constants\CommonCommands;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

//docker-compose -f common.yml -f dev.yml run --rm --name bct_main_boards_producer test btc:main_boards_producer 2

class Start extends Command {
    const COMMON_ARGS = ["docker-compose", "-f", "common.yml", "-f", "dev.yml", "run"];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::START;

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
        $this->startModule(BitcointalkCommands::MAIN_BOARDS_PRODUCER);
        $this->startModule(BitcointalkCommands::BOARD_PAGES_PRODUCER);
        $this->startModule(BitcointalkCommands::MAIN_TOPICS_PRODUCER);
        $this->startModule(BitcointalkCommands::TOPIC_PAGES_PRODUCER);
        $this->startModule(BitcointalkCommands::TOPIC_PAGES_CONSUMER);
        $this->startModule(CommonCommands::SCRAPED_RESULTS_CONSUMER);
        
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
