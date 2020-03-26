<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Console\Base\Common\KafkaConProducer;
use RdKafka\Message;

// docker-compose -f infra.yml -f backend.yml run --rm test conproducer:test

class TestKafkaConProducer extends KafkaConProducer {

    protected $signature = 'conproducer:test';
    protected $description = 'Testing kafka consumer/producer';

    public function handle() {
        $this->inputTopic = Kafka::TEST_INPUT_TOPIC;
        $this->outputTopic = Kafka::TEST_OUTPUT_TOPIC;
        $this->groupID = Kafka::TEST_INPUT_GROUP;

        parent::handle();
        
        print "Start consuming...\n";
    }
    
    protected function handleKafkaRead(Message $message) {
        $value = intval($message->payload) * 10;
        
        $this->kafkaProduce("reproduced: " . $value);
    }
}