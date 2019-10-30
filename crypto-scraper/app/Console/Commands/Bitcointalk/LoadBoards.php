<?php

namespace App\Console\Commands\Bitcointalk;

use App\Models\Pg\BoardPage;
use App\Models\Pg\MainBoard;
use App\Console\Commands\CryptoParser;

class LoadBoards extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_boards {url=https://bitcointalk.org} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads all bitcointalk boards.';

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
        
        if (self::mainBoardValid($this->url)) {
            $mainBoards = $this->loadMainBoards($this->url);
            $this->saveMainBoards($mainBoards);

            $boardPages = $this->loadBoardPages($this->url);
            $this->saveBoardPages($boardPages, $this->url);
            return 1;
        } else {
            $this->printRedLine('Invalid main board url: ' . $this->url);
            return 0;
        }
    }
    
    private function loadMainBoards(string $url): array {
        $allBoards = $this->getLinksFromPage($url, 'board');
        return self::getMainBoards($allBoards);
    }

    private function saveMainBoards(array $mainBoards) {
        $progressBar = $this->output->createProgressBar(count($mainBoards));
        foreach ($mainBoards as $board) {
            if (!MainBoard::mainBoardExists($board)) {
                $newBoard = new MainBoard();
                $newBoard->setAttribute(MainBoard::COL_URL, $board);
                $newBoard->save();
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }
    
    private function saveBoardPages(array $boardPages, string $mainUrl) {
        $pagesCount = count($boardPages);
        $progressBar = $this->output->createProgressBar($pagesCount);
        foreach ($boardPages as $key => $page) {
            if (!BoardPage::boardPageExists($page)) {
                $newBoard = new BoardPage();
                $newBoard->setAttribute(BoardPage::COL_URL, $page);
                $newBoard->setAttribute(BoardPage::COL_PARSED, false);
                $newBoard->setAttribute(BoardPage::COL_LAST, $key === $pagesCount - 1);
                $newBoard->save();
                
                $mainBoard = MainBoard::getByUrl($mainUrl);
                $mainBoard->board_pages()->save($newBoard);
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }
    
    private function loadBoardPages(string $url): array {
        $maxBoardPage = $this->getMaxPage($url);
        if ($maxBoardPage) {
            $mainBoardId = self::getMainBoardId($url);
            $fromBoardId = self::getBoardPageId($url);
            $toBoardId = self::getBoardPageId($maxBoardPage);
            
            return self::calculateBoardPages($mainBoardId, $fromBoardId, $toBoardId);
        }
        return [];
    }
    
    public static function getMainBoards(array $allBoards): array {
        return array_filter($allBoards, function (string $item) {
            return preg_match('/board=\d+\.0$/', $item, $matches) === 1;
        });
    }

    public static function mainBoardValid(string $url): bool {
        return preg_match('/board=\d+\.0$|^https:\/\/bitcointalk.org$/', $url, $matches) === 1;
    }
    
    public static function getBoardPages(array $allBoards): array {
        return array_filter($allBoards, function (string $item) {
            return preg_match('/board=\d+\.[^0]\d+$/', $item, $matches) === 1;
        });
    }
    
    public static function getBoardPageId(string $url): ?int {
        preg_match('/board=\d+\.(\d+)$/', $url, $matches);
        return $matches[1] ?? null;
    }
    
    public static function getMainBoardId(string $url): ?int {
        preg_match('/board=(\d+)\.\d+$/', $url, $matches);
        return $matches[1] ?? null;
    }
    
    public static function calculateBoardPages(int $boardId, int $from, int $to): array {
        $boardPages = [];
        for (; $from <= $to; $from += 40) {
            array_push($boardPages, sprintf('https://bitcointalk.org/index.php?board=%s.%s', $boardId, $from));
        }
        return $boardPages;
    }
}
