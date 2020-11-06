<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Console\Base\KafkaClient\KafkaConsumer;
use RdKafka\Message;

// docker-compose -f infra.yml -f maintenance.yml run --rm test consumer:test

class TestKafkaConsumer extends KafkaConsumer {

    protected $signature = 'consumer:test';
    protected $description = 'Testing kafka consumer';


    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        $this->inputTopic = Kafka::TEST_INPUT_TOPIC;
        $this->groupID = Kafka::TEST_INPUT_GROUP;
        
        parent::handle();
        
        print "Start consuming...\n";
    }
    
    protected function handleKafkaRead(Message $message) {
        $json = json_decode($message->payload);
        print "
        Received:
        Timestamp: {$message->timestamp}
        Key: {$message->key}
        Payload test: {$json->test}
        Payload asdf: {$json->asdf} \n";
    }
}