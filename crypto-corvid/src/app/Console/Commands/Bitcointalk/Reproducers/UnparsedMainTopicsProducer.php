<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Reproducers;

use App\Console\Base\Bitcointalk\UnparsedProducer;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\MainTopic;

class UnparsedMainTopicsProducer extends UnparsedProducer {

    protected $signature = BitcointalkCommands::UN_MAIN_TOPICS_PRODUCER .' {verbose=1} {--force} {dateTime?}';
    
    protected $description = BitcointalkCommands::UN_USER_PROFILES_PRODUCER_DESC;
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->outputTopic = BitcointalkKafka::MAIN_TOPICS_TOPIC;
        $this->serviceName = BitcointalkCommands::UN_MAIN_TOPICS_PRODUCER;
        $this->tableName = MainTopic::class;

        parent::handle();
    }
}