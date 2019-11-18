<?php
declare(strict_types=1);

namespace App\Console;

use Symfony\Component\DomCrawler\Crawler;

class BitcointalkParser extends CryptoParser {
    /**
     * Commands constants.
     */
    const BITCOINTALK = 'bitcointalk:';
    const BITCOINTALK_URL = 'https://bitcointalk.org';
    const RUN_BOARDS = self::BITCOINTALK . 'run_boards';
    const RUN_MAIN_TOPICS = self::BITCOINTALK . 'run_main_topics';
    const RUN_UPDATE_BOARDS = self::BITCOINTALK . 'run_update_boards';
    const RUN_TOPICS_PAGE = self::BITCOINTALK . 'run_topic_page';
    const RUN_TOPICS_PAGES = self::BITCOINTALK . 'run_topic_pages';
    const RUN_USER_PROFILES = self::BITCOINTALK . 'run_user_profiles';
    const LOAD_BOARDS = self::BITCOINTALK . 'load_boards';
    const LOAD_MAIN_TOPICS = self::BITCOINTALK . 'load_main_topics';
    const LOAD_TOPICS_PAGES = self::BITCOINTALK . 'load_topic_pages';
    const LOAD_USER_PROFILES = self::BITCOINTALK . 'load_user_profiles';
    const PARSE_USER_PROFILE = self::BITCOINTALK . 'parse_user_profile';
    const PARSE_TOPIC_MESSAGES = self::BITCOINTALK . 'parse_topic_messages';

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