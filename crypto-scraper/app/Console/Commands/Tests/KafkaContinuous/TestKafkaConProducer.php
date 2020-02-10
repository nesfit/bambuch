<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;


use App\Console\Base\Common\KafkaConProducer;
use RdKafka\Message;

class TestKafkaConProducer extends KafkaConProducer {

    protected $signature = 'conproducer:test {inputTopic} {groupID} {outputTopic}';
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