<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Parsers;

use App\Console\Base\Common\AddressMatcher;
use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\CryptoCurrency;
use App\Models\Kafka\ParsedAddress;
use App\Models\Pg\Category;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

class TopicMessages extends BitcointalkParser {
    use UrlValidations; 
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::PARSE_TOPIC_MESSAGES .' {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts crypto addresses from a single topic page url.';

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
        parent::handle();

        if (self::topicPageValid($this->url)) {
            $parsedAddresses = $this->getAddresses($this->url);
            if (count($parsedAddresses)) {
                $this->saveParsedData($this->dateTime, ...$parsedAddresses);
            }
            return 1;
        } else {
            $this->printRedLine('Invalid main topic url: ' . $this->url);
            return 0;
        }
    }

    //TODO rewrite 
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
