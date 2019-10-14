<?php

namespace App\Console\Commands;


use App\Console\CryptoCurrency;
use App\Models\ParsedAddress;
use App\Models\Pg\Category;
use Symfony\Component\DomCrawler\Crawler;

class BitcointalkParse extends CryptoParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse bitcointalk topic';

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
        
        $parsedAddresses = $this->getParsedAddresses($this->url, $source);
        $this->saveParsedData($this->dateTime, ...$parsedAddresses);
        return 1;
    }
    
    public function getParsedAddresses(string $url, string $source): array {
        $profileLinks = $this->getProfileLinks($url);
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
    
    private function getProfileLinks(string $url): array {
        $crawler = $this->getPageCrawler($url);
        $allLinks = $crawler->filterXPath('//a[contains(@href,"https://bitcointalk.org/index.php?action=profile")]/@href')->each(function (Crawler $node) {
            return $node->getNode(0)->nodeValue;
        });
        return array_unique($allLinks);
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
