<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Common\AddressMatcher;
use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkKafka;
use App\Console\Constants\CryptoCurrency;
use App\Console\Constants\CommonKafka;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;
use Illuminate\Support\Arr;
use RdKafka\Message;
use Symfony\Component\DomCrawler\Crawler;

//docker-compose -f common.yml -f dev.yml -f graylog.yml run --rm test bitcointalk:topic_pages_con_producer

class TopicPagesConsumer extends KafkaConProducer {
    use UrlValidations;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::TOPIC_PAGES_CONSUMER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load topic page from kafka and scrape it.';

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
        $this->inputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->outputTopic = CommonKafka::SCRAPE_RESULTS_TOPIC;
        $this->groupID = BitcointalkKafka::TOPIC_PAGES_GROUP;
        $this->serviceName = self::TOPIC_PAGES_CONSUMER;

        parent::handle();
        
        return 1;
    }

    protected function handleKafkaRead(Message $message) {
        $topicPageUrl = $message->payload;
        if (self::topicPageValid($topicPageUrl)) {
            $parsedAddresses = $this->getAddresses($topicPageUrl);
            print "Getting addresses: " . count($parsedAddresses) . "\n"; 
            if (count($parsedAddresses)) {
                foreach ($parsedAddresses as $item) {
                    $tsvData = $item->createTSVData();
                    var_dump($tsvData);
                    $this->kafkaProduce($tsvData);
                }
            }
            return 1;
        } else {
            $this->printRedLine('Invalid main topic url: ' . $topicPageUrl);
            return 0;
        }
    }

    /**
     * @param string $url
     * @return ParsedAddress[]
     */
    private function getAddresses(string $url): array {
        $crawler = $this->getPageCrawler($url);
        $title = $crawler->filter('title')->text();
        $results = $crawler->filter('.td_headerandpost')->each(function (Crawler $node) use($title, $url) {
            $addresses = AddressMatcher::matchAddresses($node->html());
            $userInfo = $node->previousAll()->first();
            $userName = $userInfo->filter('a')->first()->text();
            $msgURL = $node->filter('a')->first()->attr('href');

            if(count($addresses)) {
                return array_reduce($addresses, function ($acc, $address) use ($userName, $msgURL, $title, $url) {
                    array_push($acc,
                        new ParsedAddress(
                            $userName,
                            $msgURL,
                            $title,
                            $url,
                            $address,
                            CryptoCurrency::BTC["code"],
                            Category::CAT_1
                        )
                    );
                    return $acc;
                }, []);
            }
            return null;
        });
        return array_filter(Arr::flatten($results, 2));
    }

    private static function topicPageValid(string $url): bool {
        return self::pageEntityValid('topic', $url);
    }
}
