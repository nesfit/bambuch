<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\TopicPage;

class TopicPages extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::RUN_TOPICS_PAGES .' {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. self::RUN_TOPICS_PAGE .' on every unparsed topic page.';

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
        parent::handle();
        
        $mainTopics = TopicPage::getAllUnParsed();
        if (count($mainTopics)) {
            foreach ($mainTopics as $mainTopic) {
                $parsed = $this->call(self::RUN_TOPICS_PAGE, [
                    "url" => $mainTopic->getAttribute(TopicPage::COL_URL),
                    "verbose" => $this->verbose,
                    "dateTime" => $this->dateTime
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
