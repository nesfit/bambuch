<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Producers;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Base\Common\GraylogTypes;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\BoardPage;
use App\Models\Pg\Bitcointalk\MainTopic;

class MainTopicsProducer extends KafkaConProducer {
    use UrlValidations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::MAIN_TOPICS_PRODUCER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::MAIN_TOPICS_PRODUCER_DESC;

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
        $this->inputTopic = BitcointalkKafka::BOARD_PAGES_TOPIC;
        $this->outputTopic = BitcointalkKafka::MAIN_TOPICS_TOPIC;
        $this->groupID = BitcointalkKafka::BOARD_PAGES_LOAD_GROUP;
        $this->serviceName = BitcointalkCommands::MAIN_TOPICS_PRODUCER;
        $this->tableName = MainTopic::class;

        parent::handle();
        
        return 1;
    }

    protected function loadDataFromUrl(string $url): array {
        $this->infoGraylog("PHP memory allocation", GraylogTypes::INFO, memory_get_usage());
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        
        if(!BoardPage::setParsedByUrl($url)) {
            $this->warningGraylog("Couldn't find url in DB", $url);
        }
        
        return self::getMainTopics($allBoards);
    }

    protected function validateInputUrl(string $url): bool {
        return self::pageEntityValid('board', $url);
    }

    public static function getMainTopics(array $allTopics): array {
        return self::getMainEntity(self::ENTITY, $allTopics);
    }

}
