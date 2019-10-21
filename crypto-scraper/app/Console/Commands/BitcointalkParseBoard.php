<?php

namespace App\Console\Commands;


class BitcointalkParseBoard extends CryptoParser {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitcointalk:parse_board {url} {verbose=1} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load bitcointalk board.';

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

        $this->parseBoard($this->url);

        return 1;
    }
    
    
    private function parseBoard(?string $url) {
        if ($url) {
            $nextPage = $this->getNextPage($url);
            $topicLinks = $this->getLinksFromPage($url, 'topic');
            $progressBar = $this->output->createProgressBar(count($topicLinks));
            foreach ($topicLinks as $topicLink) {
                $this->call("bitcointalk:parse_topic", [
                    "url" => $topicLink,
                    "dateTime" => $this->dateTime,
                    "verbose" => $this->verbose
                ]);
                $progressBar->advance();
            }
            $this->parseBoard($nextPage);
        } 
    }
}
