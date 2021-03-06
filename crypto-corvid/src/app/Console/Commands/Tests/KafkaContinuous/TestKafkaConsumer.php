<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Console\Base\KafkaClient\KafkaConsumer;
use RdKafka\Message;

// docker-compose -f infra.yml -f backend.yml run --rm scraper consumer:test

class TestKafkaConsumer extends KafkaConsumer {

    protected $signature = 'consumer:test';
    protected $description = 'Testing kafka consumer';


    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        $this->inputTopic = Kafka::TEST_OUTPUT_TOPIC;
        $this->groupID = Kafka::TEST_OUTPUT_GROUP;
        
        parent::handle();
        
        print "Start consuming...\n";
    }
    
    protected function handleKafkaRead(Message $message) {
        print "Received: " . $message->payload . "\n";
    }
}