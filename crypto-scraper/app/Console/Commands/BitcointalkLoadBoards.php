<?php

namespace App\Console\Commands;


use App\Models\Pg\BitcointalkMainBoard;
use Symfony\Component\Console\Helper\ProgressBar;

class BitcointalkLoadBoards extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:load_boards {url=https://bitcointalk.org/} {verbose=1} {dateTime?}';

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
        
        $this->loadMainBoards($this->url);
    }
    
    private function loadMainBoards(string $url) {
        $allBoards = $this->getLinksFromPage($url, 'board');
        $mainBoards = self::getMainBoards($allBoards);
        if (count($mainBoards)) {
            $this->saveMainBoards($mainBoards);
        }
    }
    
    public static function getMainBoards(array $allBoards) {
        return array_filter($allBoards, function (string $item) {
            return preg_match('/board=\d+\.0/', $item, $matches) === 1;
        });
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
}
