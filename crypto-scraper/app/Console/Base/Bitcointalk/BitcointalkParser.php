<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\Common\CryptoParser;
use App\Console\Base\Common\StoreCrawledUrl;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use Symfony\Component\DomCrawler\Crawler;

abstract class BitcointalkParser extends CryptoParser { 
    use StoreCrawledUrl;

    private BitcointalkModel $table;

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
        $this->table = new $this->tableName();
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
    
    protected function getNewData(string $url) {
        /** @var BitcointalkModel $table */
        $table = $this->table;
        $dbData = $table::getAll();
        $all = array_map(function ($val) { return $val[BitcointalkModel::COL_URL]; }, $dbData);
        $fromUrl = $this->loadDataFromUrl($url);

        return array_diff($fromUrl, $all);
    }

    abstract protected function loadDataFromUrl(string $url): array;
}