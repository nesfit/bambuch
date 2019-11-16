<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\TopicPage;

class ParseTopicPages extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_topic_pages {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses all topics pages from DB.';

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

        $mainTopics = TopicPage::getAllUnParsed();
        if (count($mainTopics)) {
            foreach ($mainTopics as $mainTopic) {
                $parsed = $this->call("bitcointalk:parse_topic_page", [
                    "url" => $mainTopic->getAttribute(TopicPage::COL_URL),
                    "verbose" => $this->verbose
                ]);

                if ($parsed) {
                    $mainTopic->setAttribute(TopicPage::COL_PARSED, true);
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
