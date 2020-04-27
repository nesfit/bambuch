<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Common\CommonCommands;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Stop extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::STOP;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop bitcointalk containers';
    
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
        $this->stopModule(BitcointalkCommands::MAIN_BOARDS_PRODUCER);
        $this->stopModule(BitcointalkCommands::BOARD_PAGES_PRODUCER);
        $this->stopModule(BitcointalkCommands::MAIN_TOPICS_PRODUCER);
        $this->stopModule(BitcointalkCommands::TOPIC_PAGES_PRODUCER);
        $this->stopModule(BitcointalkCommands::TOPIC_PAGES_CONSUMER);
        $this->stopModule(BitcointalkCommands::USER_PROFILES_PRODUCER);
        $this->stopModule(BitcointalkCommands::USER_PROFILES_CONSUMER);
        $this->stopModule(CommonCommands::SCRAPED_RESULTS_CONSUMER);
        
        print "Ending... \n";
        return 0;
    }
    
    private function stopModule(string $module) {
        $dockerName = str_replace(":", "_", $module);
        print "Stopping/removing module: " . $dockerName . "\n";
        $process = new Process(array_merge(["docker", "rm", "-f", $dockerName]));
        $process->start();
        
        sleep(5);
    }
}
