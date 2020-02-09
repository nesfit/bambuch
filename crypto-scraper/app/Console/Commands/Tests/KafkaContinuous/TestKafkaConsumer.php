<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Kafka\KafkaConsumer;
use RdKafka\Message;

class TestKafkaConsumer extends KafkaConsumer {

    protected $signature = 'consumer:test {inputTopic} {groupID}';
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