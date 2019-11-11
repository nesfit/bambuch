<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\MainBoard;

class UpdateMainBoards extends CryptoParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:update_main_boards {verbose=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates main boards from a DB.';

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

        $allMainBoards = MainBoard::getAllBoards();
        foreach ($allMainBoards as $mainBoard) {
            $this->call("bitcointalk:load_boards", [
                "url" => $mainBoard,
                "verbose" => $this->verbose
            ]);
        }
        return 1;
    }
}