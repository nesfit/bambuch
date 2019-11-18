<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\MainBoard;

class UpdateBoards extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::RUN_UPDATE_BOARDS .' {verbose=1} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. self::LOAD_BOARDS .' on every unparsed main board.';

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

        $allMainBoards = MainBoard::getAllUnParsed();
        if (count($allMainBoards)) {
            foreach ($allMainBoards as $mainBoard) {
                $this->call(self::LOAD_BOARDS, [
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
}
