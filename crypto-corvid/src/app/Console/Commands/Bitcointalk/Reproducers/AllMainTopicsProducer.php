<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Reproducers;

use App\Console\Base\Bitcointalk\KafkaProducer;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use App\Models\Pg\Bitcointalk\MainTopic;

class AllMainTopicsProducer extends KafkaProducer {

    protected $signature = BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER .' {verbose=1} {--force} {dateTime?}';
    
    protected $description = BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER_DESC;
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->outputTopic = BitcointalkKafka::MAIN_TOPICS_TOPIC;
        $this->serviceName = BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER;
        $this->tableName = MainTopic::class;

        parent::handle();

        /**
         * All main topics has to be re-scraped to find new topic pages.
         */
        MainTopic::setParsedToAll(false);
        $unBoardPages = MainTopic::getAllUnParsed();
        foreach ($unBoardPages as $unBoardPage) {
            $parentUrl = $unBoardPage->getAttribute(BitcointalkModel::COL_PARENT_URL);
            $url = $unBoardPage->getAttribute(BitcointalkModel::COL_URL);
            $urlMessage = new UrlMessage($parentUrl, $url, false);
            $this->kafkaProduce($urlMessage->encodeData());
        }
    }
    
    protected function loadDataFromUrl(string $url): array {
        return [];
    }
}