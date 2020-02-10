<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Runners;

use App\Console\Base\Bitcointalk\BitcointalkParser;

class TopicPage extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::RUN_TOPICS_PAGE .' {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs '. self::LOAD_USER_PROFILES .' and '. self::PARSE_TOPIC_MESSAGES .' on a url.';

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
        $this->print = false;
        parent::handle();

        $this->call(self::LOAD_USER_PROFILES, [
            "url" => $this->url,
            "verbose" => $this->verbose,
            "dateTime" => $this->dateTime
        ]);

        $this->call(self::PARSE_TOPIC_MESSAGES, [
            "url" => $this->url,
            "verbose" => $this->verbose,
            "dateTime" => $this->dateTime
        ]);
        return 1;
    }
}
