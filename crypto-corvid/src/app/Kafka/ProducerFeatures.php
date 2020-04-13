<?php
declare(strict_types=1);

namespace App\Kafka;

use App\Console\Base\Common\GraylogTypes;
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
            $this->errorGraylog("'outputTopic' property is not set!");
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
        $this->infoGraylog("Producing", GraylogTypes::PRODUCED, $message);
        
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $result = $this->producer->flush(10000);

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            $error = new RuntimeException('Was unable to flush, messages might be lost!');
            $this->errorGraylog("Producer error", $error, ["failedMessage" => $message]);
            throw $error;
        }
    } 
}