<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\BoardPage;

class LoadMainTopics extends CryptoParser {
    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_main_topics {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads main topics from single board page.';

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
        
        if (self::boardPageValid($this->url)) {
            $mainTopics = $this->loadMainTopics($this->url);
            $this->saveMainTopics($mainTopics, $this->url);
            return 1;
        } else {
            $this->printRedLine('Invalid main topic url: ' . $this->url);
            return 0;
        }
    }

    private function loadMainTopics(string $url): array {
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        return self::getMainTopics($allBoards);
    }

    private function saveMainTopics(array $mainTopics, string $boardUrl) {
        $progressBar = $this->output->createProgressBar(count($mainTopics));
        
        $boardPage = BoardPage::getByUrl($boardUrl);
        if ($boardPage) {
            $boardPageId = $boardPage->getAttribute(BoardPage::COL_ID);
            foreach ($mainTopics as $topic) {
                if (!MainTopic::mainTopicExists($topic)) {
                    $mainTopic = new MainTopic();
                    $mainTopic->setAttribute(MainTopic::COL_URL, $topic);
                    $mainTopic->setAttribute(MainTopic::COL_PARSED, false);
                    $mainTopic->setAttribute(MainTopic::COL_BOARD_PAGE, $boardPageId);
                    $mainTopic->save();
                }
                $progressBar->advance();
            }
            $progressBar->finish();
            print("\n");
        } else {
            $this->printRedLine('Board page not found: ' . $boardUrl);
        }
    }

    public static function getMainTopics(array $allTopics): array {
        return Utils::getMainEntity(self::ENTITY, $allTopics);
    }

    public static function boardPageValid(string $url): bool {
        return Utils::pageEntityValid('board', $url);
    }
}
