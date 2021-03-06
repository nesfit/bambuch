<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaSingle;


use App\Console\Base\KafkaClient\KafkaConProducer;
use RdKafka\Message;

class TestKafkaConProducer extends KafkaConProducer {

    protected $signature = 'conproducer:test_single {inputTopic} {groupID} {outputTopic}';
    protected $description = 'Testing kafka consumer/producer';

    public function handle() {
        parent::handle();
        
        print "Start consuming...\n";
    }
    
    protected function handleKafkaRead(Message $message) {
        $value = intval($message->payload) * 10;
        
        $this->kafkaProduce("reproduced: " . $value);
    }
}