<?php

namespace App\Console\Commands\Bitcointalk;

use App\AddressMatcher;
use App\Console\BitcointalkParser;
use App\Console\CryptoCurrency;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;
use Illuminate\Support\Arr;
use RdKafka;
use Symfony\Component\DomCrawler\Crawler;

//require_once '/app/vendor/autoload.php';


class BoardConsumer extends BitcointalkParser {
    use UrlValidations;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::BOARD_CONSUMER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wait for url in kafka and scrape it.';

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
        parent::handle();

        $conf = new RdKafka\Conf();

        // Configure the group.id. All consumer with the same group.id will consume
        // different partitions.
        $conf->set('group.id', 'myConsumerGroup');

        // Initial list of Kafka brokers
        $conf->set('metadata.broker.list', 'kafka');

        // Set where to start consuming messages when there is no initial offset in
        // offset store or the desired offset is out of range.
        // 'smallest': start from the beginning
        $conf->set('auto.offset.reset', 'smallest');

        $consumer = new RdKafka\KafkaConsumer($conf);

        // Subscribe to topic 'test'
        $consumer->subscribe(['topic_pages']);

        echo "Waiting for partition assignment... (make take some time when\n";
        echo "quickly re-joining the group after leaving it.)\n";

        
        while (true) {
            $message = $consumer->consume(2000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $topicUrl = $message->payload;
                    if (self::topicPageValid($topicUrl)) {
                        print "Parsing url " . $topicUrl . "\n";
                        $parsedAddresses = $this->getAddresses($topicUrl);
                        if (count($parsedAddresses)) {
                            $this->saveParsedData($this->dateTime, ...$parsedAddresses);
                        }
                    } else {
                        $this->printRedLine('Invalid main topic url: ' . $topicUrl);
                    }

                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }

    }

    private function getAddresses(string $url) {
        $crawler = $this->getPageCrawler($url);
        $title = $crawler->filter('title')->text();
        $results = $crawler->filter('.td_headerandpost')->each(function (Crawler $node) use($title) {
            $addresses = AddressMatcher::matchAddresses($node->html());
            $userInfo = $node->previousAll()->first();
            $userName = $userInfo->filter('a')->first()->text();
            $msgURL = $node->filter('a')->first()->attr('href');

            if(count($addresses)) {
                return array_reduce($addresses, function ($acc, $address) use ($userName, $msgURL, $title) {
                    array_push($acc,
                        new ParsedAddress(
                            $userName,
                            $msgURL,
                            $title,
                            $this->url,
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
