<?php
declare(strict_types=1);

namespace App\Kafka;

use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RuntimeException;

abstract class KafkaProducer extends KafkaCommon {
    private Producer $producer;
    private ProducerTopic $topic;
    private string $outputTopic;

    public function __construct() {
        parent::__construct();
    }

    protected function handle() {
        $this->outputTopic = $this->argument("outputTopic");

        $config = $this->getProducerConfig();
        $this->producer = new Producer($config);

        $this->topic = $this->producer->newTopic($this->outputTopic);

        print "Going to write into '" . $this->outputTopic . "' topic \n";

        return 1;
    }
    
    protected function produce(string $message) {
        $this->topic->produce(0, 0, $message);
        $result = $this->producer->flush(10000);

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new RuntimeException('Was unable to flush, messages might be lost!');
        }
    } 
}