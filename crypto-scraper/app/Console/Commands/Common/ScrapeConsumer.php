<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\KafkaConsumer;
use App\Console\Constants\CommonKafka;
use RdKafka\Message;

//docker-compose -f common.yml -f dev.yml run --rm test scraped_results_consumer

class ScrapeConsumer extends KafkaConsumer {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SCRAPED_RESULTS_CONSUMER . '{verbose=1} {--force} {dateTime?}';

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
        $this->inputTopic = CommonKafka::SCRAPE_RESULTS_TOPIC;
        $this->groupID = CommonKafka::SCRAPE_RESULTS_GROUP;
        $this->serviceName = self::SCRAPED_RESULTS_CONSUMER;
        
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
