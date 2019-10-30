<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\CryptoCurrency;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;
use App\Console\Commands\CryptoParser;

class ParseTopic extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_topic {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse bitcointalk topic.';

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
        
        $source = $this->getFullHost();
        $this->parseTopic($this->url, $source);
        
        return 1;
    }

    /**
     * @param string|null $url
     * @param string $source
     */
    private function parseTopic(?string $url, string $source) {
        if ($url) {
            $nextPage = $this->getNextPage($url);
            $parsedAddresses = $this->getParsedAddresses($this->url, $source);
            $this->saveParsedData($this->dateTime, ...$parsedAddresses);
            $this->parseTopic($nextPage, $source);
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
}
