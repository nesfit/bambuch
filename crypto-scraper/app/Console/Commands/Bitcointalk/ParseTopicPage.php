<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;

class ParseTopicPage extends BitcointalkParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_topic_page {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract bitcoin addresses from profiles and messages.';

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

        $this->call("bitcointalk:load_user_profiles", [
            "url" => $this->url,
            "verbose" => $this->verbose
        ]);

        $this->call("bitcointalk:parse_topics_messages", [
            "url" => $this->url,
            "verbose" => $this->verbose
        ]);
        return 1;
    }
}
