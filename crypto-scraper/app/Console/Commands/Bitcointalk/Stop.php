<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use Symfony\Component\Process\Process;

class Stop extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::STOP;

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
        $this->stopModule(self::MAIN_BOARDS_PRODUCER);
        $this->stopModule(self::MAIN_BOARDS_KEEPER);
        $this->stopModule(self::BOARD_PAGES_PRODUCER);
        $this->stopModule(self::BOARD_PAGES_KEEPER);
        $this->stopModule(self::MAIN_TOPICS_PRODUCER);
        $this->stopModule(self::MAIN_TOPICS_KEEPER);
        $this->stopModule(self::TOPIC_PAGES_PRODUCER);
        $this->stopModule(self::TOPIC_PAGES_KEEPER);
        
        print "Ending... \n";
        return 0;
    }
    
    private function stopModule(string $module) {
        $dockerName = str_replace(":", "_", $module);
        print "Stopping module: " . $dockerName . "\n";
        $process = new Process(array_merge(["docker", "stop", $dockerName]));
        $process->start();
        
        sleep(5);
    }
}
