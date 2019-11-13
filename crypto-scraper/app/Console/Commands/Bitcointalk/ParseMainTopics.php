<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\Commands\CryptoParser;
use App\Models\Pg\Bitcointalk\MainTopic;

class ParseMainTopics extends CryptoParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_main_topics {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load bitcointalk topic pages from all main topics.';

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

        $mainTopics = MainTopic::getUnParsedTopics();
        foreach ($mainTopics as $mainTopic) {
            $parsed = $this->call("bitcointalk:load_topic_pages", [
                "url" => $mainTopic->getAttribute(MainTopic::COL_URL),
                "verbose" => $this->verbose
            ]);

            if ($parsed) {
                $mainTopic->setAttribute(MainTopic::COL_PARSED, true);
                $mainTopic->save();
            }
        }
    }
}
