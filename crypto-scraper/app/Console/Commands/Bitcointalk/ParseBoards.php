<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Models\Pg\BoardPage;

class ParseBoards extends BitcointalkParser {
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
    protected $description = 'Load bitcointalk main topics from all board pages.';

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
        if (count($boardPages)) {
            foreach ($boardPages as $boardPage) {
                $parsed = $this->call("bitcointalk:load_main_topics", [
                    "url" => $boardPage->getAttribute(BoardPage::COL_URL),
                    "verbose" => $this->verbose
                ]);
                
                if ($parsed) {
                    $boardPage->setAttribute(BoardPage::COL_PARSED, true);
                    $boardPage->save();
                }
            }
            return 1;
        } else {
            $this->printRedLine("No unparsed boards found!");
            return 0;
        }
    }
}
