<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Models\Pg\BoardPage;

class UpdateMainTopics extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:update_main_topics {verbose=1} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $this->call("bitcointalk:parse_boards", [
            "verbose" => $this->verbose
        ]);
        return 1;
    }
}
