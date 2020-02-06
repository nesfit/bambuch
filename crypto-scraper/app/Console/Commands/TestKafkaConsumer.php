<?php


namespace App\Console\Commands;


use App\Kafka\KafkaConsumer;
use RdKafka\Message;

class TestKafkaConsumer extends KafkaConsumer {

    protected $signature = 'consumer:test {topicName} {groupID}';
    protected $description = 'Testing kafka consumer';


    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
        
        print "Start consuming...\n";
    }
    
    protected function handleKafkaRead(Message $message) {
        print "Received: " . $message->payload . "\n";
    }
}