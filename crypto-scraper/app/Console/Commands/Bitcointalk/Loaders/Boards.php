<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Loaders;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Models\Pg\Bitcointalk\BoardPage;
use App\Models\Pg\Bitcointalk\MainBoard;

class Boards extends BitcointalkParser {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'board';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::LOAD_BOARDS .' {url='. self::BITCOINTALK_URL .'} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads main boards and board pages from a url (or from '. self::BITCOINTALK_URL .').';

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
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        return self::getMainBoards($allBoards);
    }

    private function saveMainBoards(array $mainBoards) {
        $progressBar = $this->output->createProgressBar(count($mainBoards));
        foreach ($mainBoards as $board) {
            if (!MainBoard::exists($board)) {
                $mainBoard = new MainBoard();
                $mainBoard->setAttribute(MainBoard::COL_URL, $board);
                $mainBoard->setAttribute(MainBoard::COL_PARSED, false);
                $mainBoard->save();
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }

    private function saveBoardPages(array $boardPages, string $mainUrl) {
        $mainBoard = MainBoard::getByUrl($mainUrl);
        if ($mainBoard) {
            $mainBoardId = $mainBoard->getAttribute(MainBoard::COL_ID);
            // TODO BUG FIX: when no new board pages, no row with last=true in DB
            BoardPage::unsetLast($mainBoardId);

            $pagesCount = count($boardPages);
            $progressBar = $this->output->createProgressBar($pagesCount);
            foreach ($boardPages as $key => $page) {
                if (!BoardPage::exists($page)) {
                    $newBoard = new BoardPage();
                    $newBoard->setAttribute(BoardPage::COL_URL, $page);
                    $newBoard->setAttribute(BoardPage::COL_PARSED, false);
                    $newBoard->setAttribute(BoardPage::COL_PARENT_ID, $mainBoardId);
                    $newBoard->setAttribute(BoardPage::COL_LAST, $key === $pagesCount - 1);
                    $newBoard->save();
                }
                $progressBar->advance();
            }
            $mainBoard->setAttribute(MainBoard::COL_PARSED, true);
            $mainBoard->save();

            $progressBar->finish();
            print("\n");
        } else if ($mainUrl === self::BITCOINTALK_URL) {
            $this->printCyanLine('Peacefully ending...');
        } else {
            $this->printRedLine('Main board not found: ' . $mainUrl);
        }
    }

    private function loadBoardPages(string $url): array {
        $maxBoardPage = $this->getMaxPage($url);
        if ($maxBoardPage) {
            $mainBoardId = self::getMainBoardId($url);
            $fromBoardId = self::getBoardPageId($url);
            $toBoardId = self::getBoardPageId($maxBoardPage);

            return self::calculateBoardPages($mainBoardId, $fromBoardId, $toBoardId);
        }
        return [$url];
    }

    public static function getMainBoards(array $allBoards): array {
        return self::getMainEntity(self::ENTITY, $allBoards);
    }

    public static function mainBoardValid(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }

    public static function getBoardPages(array $allBoards): array {
        return self::getEntityPages(self::ENTITY, $allBoards);
    }

    public static function getBoardPageId(string $url): ?int {
        return self::getEntityPageId(self::ENTITY, $url);
    }

    public static function getMainBoardId(string $url): ?int {
        return self::getMainEntityId(self::ENTITY, $url);
    }

    public static function calculateBoardPages(int $boardId, int $from, int $to): array {
        return self::calculateEntityPages(self::ENTITY, $boardId, $from, $to);
    }
}
