<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Loaders;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\TopicPage;

class TopicPages extends BitcointalkParser {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::LOAD_TOPICS_PAGES .' {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads all topic pages from a main topic url.';

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
        $mainTopic = MainTopic::getByUrl($mainUrl);
        if ($mainTopic) {
            $mainTopicId = $mainTopic->getAttribute(MainTopic::COL_ID);
            TopicPage::unsetLast($mainTopicId);

            $pagesCount = count($boardPages);
            $progressBar = $this->output->createProgressBar($pagesCount);
            foreach ($boardPages as $key => $page) {
                if (!TopicPage::exists($page)) {
                    $topicPage = new TopicPage();
                    $topicPage->setAttribute(TopicPage::COL_URL, $page);
                    $topicPage->setAttribute(TopicPage::COL_PARSED, false);
                    $topicPage->setAttribute(TopicPage::COL_PARENT_URL, $mainTopicId);
                    $topicPage->setAttribute(TopicPage::COL_LAST, $key === $pagesCount - 1);
                    $topicPage->save();
                }
                $mainTopic->setAttribute(MainTopic::COL_PARSED, true);
                $mainTopic->save();

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
        return [$url];
    }

    public static function mainTopicValid(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }

    public static function getTopicPageId(string $url): ?int {
        return self::getEntityPageId(self::ENTITY, $url);
    }

    public static function getMainTopicId(string $url): ?int {
        return self::getMainEntityId(self::ENTITY, $url);
    }

    public static function calculateTopicPages(int $topicId, int $from, int $to): array {
        return self::calculateEntityPages(self::ENTITY, $topicId, $from, $to);
    }
}
