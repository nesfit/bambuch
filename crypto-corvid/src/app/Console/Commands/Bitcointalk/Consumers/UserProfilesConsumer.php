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
use App\Models\Pg\Bitcointalk\UserProfile;
use App\Models\Pg\Category;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

//docker-compose -f infra.yml -f backend.yml run --rm scraper bct:user_profiles_consumer 2

class UserProfilesConsumer extends KafkaConProducer {
    use UrlValidations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::USER_PROFILES_CONSUMER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::USER_PROFILES_CONSUMER_DESC;

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
        $this->inputTopic = BitcointalkKafka::USER_PROFILES_TOPIC;
        $this->outputTopic = BitcointalkKafka::SCRAPE_RESULTS_TOPIC;
        $this->groupID = BitcointalkKafka::USER_PROFILES_LOAD_GROUP;
        $this->serviceName = BitcointalkCommands::USER_PROFILES_CONSUMER;
        $this->tableName = UserProfile::class;

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

        if(!UserProfile::setParsedByUrl($mainUrl)) {
           $this->warningGraylog("Couldn't find url in DB", $mainUrl); 
        
        }

        return 1;
    }

    /**
     * @param string $url
     * @return ParsedAddress[]
     */
    protected function loadDataFromUrl(string $url): array {
        $source = $this->getFullHost($url);
        list($name, $address) = $this->parseProfile($url);
        if ($name) {
            return [
                new ParsedAddress(
                    $name,
                    $url,
                    '',
                    $source,
                    $address,
                    CryptoCurrency::BTC["code"],
                    Category::CAT_2
                )
            ];
        }
        return [];
    }

    protected function validateInputUrl(string $url): bool {
        return preg_match('/https:\/\/bitcointalk.org\/index.php\?action=profile;u=\d+$/', $url, $matches) === 1;
    }

    private function parseProfile(string $url): array {
        $crawler = $this->getPageCrawler($url);
        $addressNode = $crawler->filterXPath('//text()[contains(.,"Bitcoin address: ")]/../../../td[last()]')->getNode(0);
        if ($addressNode) {
            $name = $crawler->filterXPath('//text()[contains(.,"Name")]/../../../td[last()]')->text();
            $address = $addressNode->nodeValue;
            return [$name,$address];
        }
        return [null,null];
    }
}
