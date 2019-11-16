<?php


namespace App\Console;

use App\Console\Commands\CryptoParser;
use Symfony\Component\DomCrawler\Crawler;

class BitcointalkParser extends CryptoParser {

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
    }
    
    /**
     * @param string $url "board|topic|action=profile"
     * @param string $pageType
     * @return array
     */
    protected function getLinksFromPage(string $url, string $pageType): array {
        $crawler = $this->getPageCrawler($url);
        $allLinks = $crawler->filterXPath('//a[contains(@href,"https://bitcointalk.org/index.php?' . $pageType. '")]/@href')->each(function (Crawler $node) {
            return $node->getNode(0)->nodeValue;
        });
        return array_unique($allLinks);
    }
    
    protected function getMaxPage(string $url): ?string {
        $crawler = $this->getPageCrawler($url);
        $node = $crawler->filterXPath('//td/a[@class="navPages"][last()]/@href')->getNode(0);

        if ($node) {
            $nextPage = $node->nodeValue;
            $this->printVerbose3("<fg=blue>Max page: " . $nextPage ."</>");
            return $nextPage;
        }

        return null;
    }
    
    protected function getNextPage(string $url): ?string {
        $crawler = $this->getPageCrawler($url);
        $node = $crawler->filterXPath('//span[@class="prevnext"][2]/a/@href')->getNode(0);

        if ($node) {
            $nextPage = $node->nodeValue;
            $this->printVerbose3("<fg=blue>Next page: " . $nextPage ."</>");
            return $nextPage;
        }

        return null;
    }
}