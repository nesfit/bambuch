<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Console\CryptoCurrency;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;

class ParseTopicPage extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_topic_page {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract bitcoin addresses from profiles and messages.';

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
            $source = $this->getFullHost();
            $parsedAddresses = $this->getParsedAddresses($this->url, $source);
            $this->saveParsedData($this->dateTime, ...$parsedAddresses);
            return 1;
        } else {
            $this->printRedLine('Invalid topic page url: ' . $this->url);
            return 0;
        }
    }
        
    public function getParsedAddresses(string $url, string $source): array {
        $profileLinks = $this->getLinksFromPage($url, 'action=profile');
        if ($profileLinks) {
            $maybeNull = array_map(function ($url) use ($source) {
                list($name, $address) = $this->parseProfile($url);

                if ($name) {
                    return new ParsedAddress(
                        $name,
                        $url,
                        '',
                        $source,
                        $address,
                        CryptoCurrency::BTC["code"],
                        Category::CAT_2
                    );
                }
                return null;
            }, $profileLinks);
            return array_filter($maybeNull, function ($i) { return $i !== null; });
        }
        return [];
    }
        
    private function parseProfile(string $url): array {
        $this->printVerbose2("<fg=white>Parsing profile: ". $url . "</>");

        $crawler = $this->getPageCrawler($url);
        $addressNode = $crawler->filterXPath('//text()[contains(.,"Bitcoin address: ")]/../../../td[last()]')->getNode(0);
        if ($addressNode) {
            $name = $crawler->filterXPath('//text()[contains(.,"Name")]/../../../td[last()]')->text();
            $address = $addressNode->nodeValue;
            return [$name,$address];
        }
        return [null,null];
    }
    
    private static function topicPageValid(string $url): bool {
        return Utils::pageEntityValid('topic', $url);
    }
}
