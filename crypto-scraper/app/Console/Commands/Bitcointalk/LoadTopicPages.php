<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\TopicPage;

class LoadTopicPages extends CryptoParser {
    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_topic_pages {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads all topic pages from main topic.';

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

        if (self::mainTopicValid($this->url)) {
            $topicPages = $this->loadTopicPages($this->url);
            $this->saveTopicPages($topicPages, $this->url);
            return 1;
        } else {
            $this->printRedLine('Invalid main topic url: ' . $this->url);
            return 0;
        }
    }

    private function saveTopicPages(array $boardPages, string $mainUrl) {
        $mainBoard = MainTopic::getByUrl($mainUrl);
        if ($mainBoard) {
            TopicPage::unsetLastTopic($mainBoard->getAttribute(MainTopic::COL_ID));
    
            $pagesCount = count($boardPages);
            $progressBar = $this->output->createProgressBar($pagesCount);
            foreach ($boardPages as $key => $page) {
                if (!TopicPage::topicPageExists($page)) {
                    $newBoard = new TopicPage();
                    $newBoard->setAttribute(TopicPage::COL_URL, $page);
                    $newBoard->setAttribute(TopicPage::COL_PARSED, false);
                    $newBoard->setAttribute(TopicPage::COL_LAST, $key === $pagesCount - 1);
                    $newBoard->save();
    
                    $mainBoard->board_topics()->save($newBoard);
                }
                $mainBoard->setAttribute(MainTopic::COL_PARSED, true);
                $mainBoard->save();
    
                $progressBar->advance();
            }
            $progressBar->finish();
            print("\n");
        } else {
            $this->printRedLine('Main topic not found: ' . $mainUrl);
        }
    }

    private function loadTopicPages(string $url): array {
        $maxTopicPage = $this->getMaxPage($url);
        if ($maxTopicPage) {
            $mainTopicId = self::getMainTopicId($url);
            $fromTopicId = self::getTopicPageId($url);
            $toTopicId = self::getTopicPageId($maxTopicPage);

            return self::calculateTopicPages($mainTopicId, $fromTopicId, $toTopicId);
        }
        return [];
    }

    public static function mainTopicValid(string $url): bool {
        return Utils::mainEntityValid(self::ENTITY, $url);
    }

    public static function getTopicPageId(string $url): ?int {
        return Utils::getEntityPageId(self::ENTITY, $url);
    }

    public static function getMainTopicId(string $url): ?int {
        return Utils::getMainEntityId(self::ENTITY, $url);
    }

    public static function calculateTopicPages(int $topicId, int $from, int $to): array {
        return Utils::calculateEntityPages(self::ENTITY, $topicId, $from, $to);
    }
}
