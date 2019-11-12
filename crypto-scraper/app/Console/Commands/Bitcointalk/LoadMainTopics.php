<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\Bitcointalk\MainTopic;

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
            $this->saveMainTopics($mainTopics);
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

    private function saveMainTopics(array $mainBoards) {
        $progressBar = $this->output->createProgressBar(count($mainBoards));
        foreach ($mainBoards as $board) {
            if (!MainTopic::mainTopicExists($board)) {
                $mainBoard = new MainTopic();
                $mainBoard->setAttribute(MainTopic::COL_URL, $board);
                $mainBoard->setAttribute(MainTopic::COL_PARSED, false);
                $mainBoard->save();
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }

    public static function getMainTopics(array $allTopics): array {
        return Utils::getMainEntity(self::ENTITY, $allTopics);
    }

    public static function boardPageValid(string $url): bool {
        return Utils::pageEntityValid('board', $url);
    }
}
