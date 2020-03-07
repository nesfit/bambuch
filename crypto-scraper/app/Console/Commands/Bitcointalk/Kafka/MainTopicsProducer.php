<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Common\StoreCrawledUrl;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkKafka;
use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\MainTopic;
use RdKafka\Message;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:main_topics_producer 2

class MainTopicsProducer extends KafkaConProducer {
    use UrlValidations;
    use StoreCrawledUrl;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::MAIN_TOPICS_PRODUCER .' {verbose=1} {--force} {dateTime?}';

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
        $this->serviceName = self::MAIN_TOPICS_PRODUCER;
        $this->tableName = MainTopic::class;

        parent::handle();
        
        return 1;
    }

    protected function handleKafkaRead(Message $message) {
        $inUrlMessage = KafkaUrlMessage::decodeData($message->payload);
        $boardPageUrl = $inUrlMessage->url;

        if (self::boardPageValid($boardPageUrl)) {
            $mainTopics = $this->loadMainTopics($boardPageUrl);
            foreach ($mainTopics as $mainTopic) {
                $outUrlMessage = new KafkaUrlMessage($boardPageUrl, $mainTopic, false);
                $this->storeChildUrl($outUrlMessage);
                $this->kafkaProduce($outUrlMessage->encodeData());
            }
            return 0;
        } else {
            $this->warningGraylog('Invalid board page url', $boardPageUrl);
            return 1;
        }
    }

    private function loadMainTopics(string $url): array {
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        return self::getMainTopics($allBoards);
    }

    public static function getMainTopics(array $allTopics): array {
        return self::getMainEntity(self::ENTITY, $allTopics);
    }

    public static function boardPageValid(string $url): bool {
        return self::pageEntityValid('board', $url);
    }
}
