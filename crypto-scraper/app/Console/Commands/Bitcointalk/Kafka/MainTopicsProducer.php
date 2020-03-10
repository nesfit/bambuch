<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkCommands;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\MainTopic;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:main_topics_producer 2

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
    protected $description = 'Load main topics from board page.';

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
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        return self::getMainTopics($allBoards);
    }

    protected function validateInputUrl(string $url): bool {
        return self::pageEntityValid('board', $url);
    }

    public static function getMainTopics(array $allTopics): array {
        return self::getMainEntity(self::ENTITY, $allTopics);
    }

}
