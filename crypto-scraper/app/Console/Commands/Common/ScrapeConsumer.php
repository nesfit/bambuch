<?php

namespace App\Console\Commands\Common;

use App\Console\Base\Common\KafkaConsumer;
use RdKafka\Message;

//docker-compose -f common.yml -f dev.yml run --rm test php artisan consumer:scrape scrapeTopic scrapeGroup

class ScrapeConsumer extends KafkaConsumer {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer:scrape {inputTopic} {groupID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store scraped information into PG DB.';

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
        return 1;
    }
    
    protected function handleKafkaRead(Message $message) {
        list($owner, $url, $label, $source, $address, $cryptoType, $category) = explode("\t", $message->payload); 
        print "Inserting: " . $url . "\n";
        
        $this->call('insert:db', [
            'owner name' => $owner,
            'url' => $url,
            'label' => $label,
            'source' => $source,
            'address' => $address,
            'crypto type' => $cryptoType,
            'category' => $category
        ]);
    }
}
