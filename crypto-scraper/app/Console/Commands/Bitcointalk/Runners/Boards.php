<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Constants\BitcointalkCommands;
use App\Models\Pg\Bitcointalk\BoardPage;

class Boards extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::RUN_BOARDS .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. BitcointalkCommands::LOAD_MAIN_TOPICS .' on every unparsed board page.';

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

        if($this->option("force")) {
            $this->printCyanLine("Force update!");
            BoardPage::setParsedToAll(false);
        }
        
        $boardPages = BoardPage::getAllUnParsed();
        if (count($boardPages)) {
            foreach ($boardPages as $boardPage) {
                $parsed = $this->call(BitcointalkCommands::LOAD_MAIN_TOPICS, [
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

    protected function loadDataFromUrl(string $url): array {
        return [];
    }
}
