<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\MainTopic;

class MainTopics extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::RUN_MAIN_TOPICS .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. self::LOAD_TOPICS_PAGES .' on every unparsed main topic.';

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
            MainTopic::setParsedToAll(false);
        }

        $mainTopics = MainTopic::getAllUnParsed();
        if (count($mainTopics)) {
            foreach ($mainTopics as $mainTopic) {
                $parsed = $this->call(self::LOAD_TOPICS_PAGES, [
                    "url" => $mainTopic->getAttribute(MainTopic::COL_URL),
                    "verbose" => $this->verbose
                ]);
    
                if ($parsed) {
                    $mainTopic->setAttribute(MainTopic::COL_PARSED, true);
                    $mainTopic->save();
                }
            }
            return 1;
        } else {
            $this->printRedLine("No unparsed main topics found!");
            return 0;
        }
    }
}
