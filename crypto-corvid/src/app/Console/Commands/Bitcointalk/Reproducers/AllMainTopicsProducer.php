<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Reproducers;

use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Models\Pg\Bitcointalk\MainTopic;
use Illuminate\Console\Command;

class AllMainTopicsProducer extends Command {

    protected $signature = BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER .' {verbose=1} {--force} {dateTime?}';
    
    protected $description = BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER_DESC;
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {

        /**
         * All main topics has to be re-scraped to find new topic pages.
         */
        MainTopic::setParsedToAll(false);
        $this->call(BitcointalkCommands::UN_MAIN_TOPICS_PRODUCER);
    }
}