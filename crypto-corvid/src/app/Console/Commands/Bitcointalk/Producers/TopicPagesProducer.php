<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Producers;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Bitcointalk\UrlCalculations;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\TopicPage;

class TopicPagesProducer extends KafkaConProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::TOPIC_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::TOPIC_PAGES_PRODUCER_DESC;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->inputTopic = BitcointalkKafka::MAIN_TOPICS_TOPIC;
        $this->outputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->groupID = BitcointalkKafka::MAIN_TOPICS_LOAD_GROUP;
        $this->serviceName = BitcointalkCommands::TOPIC_PAGES_PRODUCER;
        $this->tableName = TopicPage::class;

        parent::handle();

        return 1;
    }

    protected function loadDataFromUrl(string $url): array {
        $maxTopicPage = $this->getMaxPage($url);
        if ($maxTopicPage) {
            $mainTopicId = self::getMainTopicId($url);
            $fromTopicId = self::getTopicPageId($url);
            $toTopicId = self::getTopicPageId($maxTopicPage);

            /**
             * Last topic page has to be re-scraped again for possible new messages.
             */
            $lastTopicPage = TopicPage::getLast($url);
            // TODO should always get one => LOG if not
            if ($lastTopicPage) {
                TopicPage::unparseLast($url);
                $lastUrl = $lastTopicPage->getAttribute(BitcointalkModel::COL_URL);
                $outUrlMessage = new UrlMessage($url, $lastUrl, true);
                $this->kafkaProduce($outUrlMessage->encodeData());
            }

            if(!MainTopic::setParsedByUrl($url)) {
                $this->warningGraylog("Couldn't find url in DB", ["url" => $url]);
            }
            
            return self::calculateTopicPages($mainTopicId, $fromTopicId, $toTopicId);
        }

        return [$url];
    }

    protected function validateInputUrl(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }

    private static function getTopicPageId(string $url): ?int {
        return self::getEntityPageId(self::ENTITY, $url);
    }

    private static function getMainTopicId(string $url): ?int {
        return self::getMainEntityId(self::ENTITY, $url);
    }

    private static function calculateTopicPages(int $topicId, int $from, int $to): array {
        return self::calculateEntityPages(self::ENTITY, $topicId, $from, $to, 20);
    }
}
