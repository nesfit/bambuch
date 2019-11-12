<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\BoardPage;

class ParseBoards extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_boards {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load bitcointalk topics from all boards.';

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
        $this->verbose = $this->argument("verbose");

        $boardPages = BoardPage::getUnparsedBoardPages();
        foreach ($boardPages as $boardPage) {;
            $this->call("bitcointalk:load_main_topics", [
                "url" => $boardPage->url,
                "verbose" => $this->verbose
            ]);
        }
        return 1;
    }
}
