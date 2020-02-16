<?php
declare(strict_types=1);

namespace App\Kafka;

use RdKafka\Producer;
use RdKafka\ProducerTopic;
use RuntimeException;

trait ProducerFeatures {
    use CommonFeatures;
    
    private Producer $producer;
    private ProducerTopic $topic;
    protected string $outputTopic;

    protected function initProducer() {
        if (!isset($this->outputTopic)) {
            $this->error("'outputTopic' property is not set!");
            exit(0);
        }

        $config = $this->getProducerConfig();
        $this->producer = new Producer($config);

        $this->topic = $this->producer->newTopic($this->outputTopic);

        if ($this->verbose > 1) {
            print "Going to write into '" . $this->outputTopic . "' topic \n";
        }
    }
    
    protected function kafkaProduce(string $message) {
        if ($this->verbose > 1) {
            print "Producing message: " . $message . "\n";
        }
        
        $this->topic->produce(0, 0, $message);
        $result = $this->producer->flush(10000);

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new RuntimeException('Was unable to flush, messages might be lost!');
        }
    } 
}