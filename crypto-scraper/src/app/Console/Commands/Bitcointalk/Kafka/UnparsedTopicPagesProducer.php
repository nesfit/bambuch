<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\UnparsedProducer;
use App\Console\Constants\BitcointalkCommands;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\TopicPage;

class UnparsedTopicPagesProducer extends UnparsedProducer {

    protected $signature = BitcointalkCommands::UN_TOPIC_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';
    
    protected $description = BitcointalkCommands::UN_TOPIC_PAGES_PRODUCER_DESC;
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->outputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->serviceName = BitcointalkCommands::UN_TOPIC_PAGES_PRODUCER;
        $this->tableName = TopicPage::class;

        parent::handle();
    }
}