<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\DBLoaders;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\BoardPage;

class MainTopics extends BitcointalkParser {
    use UrlValidations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::LOAD_MAIN_TOPICS .' {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads main topics from a single board page url.';

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
            $this->printRedLine('Invalid board page url: ' . $this->url);
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
                if (!MainTopic::exists($topic)) {
                    $mainTopic = new MainTopic();
                    $mainTopic->setAttribute(MainTopic::COL_URL, $topic);
                    $mainTopic->setAttribute(MainTopic::COL_PARSED, false);
                    $mainTopic->setAttribute(MainTopic::COL_BOARD_PAGE, $boardPageId);
                    $mainTopic->save();
                }
                $progressBar->advance();
            }
            $boardPage->setAttribute(BoardPage::COL_PARSED, true);
            $boardPage->save();
            
            $progressBar->finish();
            print("\n");
        } else {
            $this->printRedLine('Board page not found: ' . $boardUrl);
        }
    }

    public static function getMainTopics(array $allTopics): array {
        return self::getMainEntity(self::ENTITY, $allTopics);
    }

    public static function boardPageValid(string $url): bool {
        return self::pageEntityValid('board', $url);
    }
}
