<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\Base\Bitcointalk\BitcointalkParser;
use App\Console\Constants\BitcointalkCommands;
use App\Models\Pg\Bitcointalk\MainBoard;

class InitializeBoards extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::INITIALIZE_BOARDS .' {verbose=1} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. BitcointalkCommands::LOAD_BOARDS .' on every unparsed main board.';

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
            MainBoard::setParsedToAll(false);
        }

        // always check for new boards in bitcointalk index
        $this->call(BitcointalkCommands::LOAD_BOARDS, [
            "url" => BitcointalkCommands::BITCOINTALK_URL,
            "verbose" => $this->verbose
        ]);
        
        // continue with unparsed main boards
        $allMainBoards = MainBoard::getAllUnParsed();
        if (count($allMainBoards)) {
            foreach ($allMainBoards as $mainBoard) {
                $this->call(BitcointalkCommands::LOAD_BOARDS, [
                    "url" => $mainBoard->getAttribute(MainBoard::COL_URL),
                    "verbose" => $this->verbose
                ]);
            }
            return 1;
        } else {
            $this->printRedLine("No unparsed main boards!");
            return 0;
        }
    }

    protected function loadDataFromUrl(string $url): array {
        return [];
    }
}
