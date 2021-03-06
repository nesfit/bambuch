<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Consumers;

use App\Console\Base\Common\AddressMatcher;
use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Console\Constants\Common\CryptoCurrency;
use App\Console\Constants\Common\CommonKafka;
use App\Models\Kafka\ParsedAddress;
use App\Models\Pg\Bitcointalk\TopicPage;
use App\Models\Pg\Category;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

class TopicPagesConsumer extends KafkaConProducer {
    use UrlValidations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::TOPIC_PAGES_CONSUMER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::TOPIC_PAGES_CONSUMER_DESC;

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
        $this->groupID = BitcointalkKafka::TOPIC_PAGES_ADDR_GROUP;
        $this->serviceName = BitcointalkCommands::TOPIC_PAGES_CONSUMER;
        // TODO getNewData() will always return whole array so the function (neither this property) is not necessary at all
        $this->tableName = TopicPage::class;  

        parent::handle();
        
        return 1;
    }

    protected function processInputUrl(string $mainUrl) {
        $parsedAddresses = $this->loadDataFromUrl($mainUrl);
        if (count($parsedAddresses)) {
            foreach ($parsedAddresses as $item) {
                $tsvData = $item->createTSVData();
                $this->kafkaProduce($tsvData);
            }
        }

        if(!TopicPage::setParsedByUrl($mainUrl)) {
           $this->warningGraylog("Couldn't find url in DB", ["url" => $mainUrl]); 
        
        }

        return 1;
    }

    /**
     * @param string $url
     * @return ParsedAddress[]
     */
    protected function loadDataFromUrl(string $url): array {
        $crawler = $this->getPageCrawler($url);
        try {
            $title = $crawler->filter('title')->text();
            $results = $crawler->filter('.td_headerandpost')->each(function (Crawler $node) use($title, $url) {
                $addresses = array_keys(AddressMatcher::matchAddresses($node->html()));
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
        } catch(\Exception $e) {
            $this->errorGraylog("Goutte failed - loadDataFromUrl", $e, ["url" => $url]);
            return [];
        }
    }

    protected function validateInputUrl(string $url): bool {
        return self::pageEntityValid(self::ENTITY, $url);
    }
}
