<?php

namespace App\Console\Commands;

use App\Kafka\KafkaConsumer;
use RdKafka\Message;

class ScrapeConsumer extends KafkaConsumer {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer:scrape {groupID} {topicName}';

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
        parent::handle();
        return 1;
    }
    
    protected function handleKafkaRead(Message $message) {
        list($owner, $url, $label, $source, $address, $cryptoType, $category) = explode("\t", $message->payload); 
        
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
