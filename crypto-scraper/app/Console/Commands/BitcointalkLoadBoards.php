<?php

namespace App\Console\Commands;


use App\Models\Pg\BitcointalkMainBoard;

class BitcointalkLoadBoards extends CryptoParser {
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
            if (count($mainBoards)) {
                $this->saveMainBoards($mainBoards);
            }
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
            if (!BitcointalkMainBoard::mainBoardExists($board)) {
                $newBoard = new BitcointalkMainBoard();
                $newBoard->setAttribute('url', $board);
                $newBoard->save();
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        print("\n");
    }
    
    private function getLastBoardPage(string $url) {

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
    
    public static function getBoardId(string $boardPage): ?int {
        preg_match('/board=\d+\.(\d+)$/', $boardPage, $matches);
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
