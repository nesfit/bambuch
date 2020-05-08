<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\Common\CryptoParser;
use App\Console\Base\Common\GraylogTypes;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use Symfony\Component\DomCrawler\Crawler;

abstract class BitcointalkParser extends CryptoParser {
    private BitcointalkModel $table;
    protected string $tableName;

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();

        $this->table = new $this->tableName();
    }

    private function checkTable() {
        if (!isset($this->tableName)) {
            $this->errorGraylog("'tableName' property is not set!");
            exit(0);
        }
    }

    protected function storeMainUrl(UrlMessage $message) {
        $this->checkTable();

        /**
         * @var $entity BitcointalkModel
         */
        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkModel::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkModel::COL_PARSED, false);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }

    protected function storeChildUrl(UrlMessage $message) {
        $this->checkTable();

        /**
         * @var $entity BitcointalkModel
         */
        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkModel::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkModel::COL_PARSED, false);
        $entity->setAttribute(BitcointalkModel::COL_PARENT_URL, $message->mainUrl);
        $entity->setAttribute(BitcointalkModel::COL_LAST, $message->last);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }
    
    /**
     * @param string $url "board|topic|action=profile"
     * @param string $pageType
     * @return array
     */
    protected function getLinksFromPage(string $url, string $pageType): array {
        try{
            $crawler = $this->getPageCrawler($url);
            $allLinks = $crawler->filterXPath('//a[contains(@href,"https://bitcointalk.org/index.php?' . $pageType. '")]/@href')->each(function (Crawler $node) {
                return $node->getNode(0)->nodeValue;
            });
            return array_unique($allLinks);
        } catch(\Exception $e) {
            $this->errorGraylog("Goutte failed - getLinksFromPage", $e);
            return [];
        }
    }
    
    protected function getMaxPage(string $url): ?string {
        try {
            $crawler = $this->getPageCrawler($url);
            $node = $crawler->filterXPath('//td/a[@class="navPages"][last()]/@href')->getNode(0);
        } catch(\Exception $e) {
            $this->errorGraylog("Goutte failed - getMaxPage", $e);
            return null;
        }
        
        
        if ($node) {
            $nextPage = $node->nodeValue;
            $this->printVerbose3("<fg=blue>Max page: " . $nextPage ."</>");
            return $nextPage;
        }

        return null;
    }
    
    protected function getNextPage(string $url): ?string {
        try {
            $crawler = $this->getPageCrawler($url);
            $node = $crawler->filterXPath('//span[@class="prevnext"][2]/a/@href')->getNode(0);
        } catch(\Exception $e) {
            $this->errorGraylog("Goutte failed - getNextPage", $e);
            return null;
        }


        if ($node) {
            $nextPage = $node->nodeValue;
            $this->printVerbose3("<fg=blue>Next page: " . $nextPage ."</>");
            return $nextPage;
        }

        return null;
    }
    
    protected function getNewData(string $url) {
        $table = $this->table;
        /** @var BitcointalkModel $table */
        $dbData = $table::getAll();
        $all = array_map(function ($val) { return $val[BitcointalkModel::COL_URL]; }, $dbData);
        $fromUrl = $this->loadDataFromUrl($url);

        return array_diff($fromUrl, $all);
    }

    abstract protected function loadDataFromUrl(string $url): array;
}