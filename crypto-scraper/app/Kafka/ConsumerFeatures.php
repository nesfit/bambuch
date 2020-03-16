<?php
declare(strict_types=1);

namespace App\Kafka;

use App\Console\Base\Common\GraylogTypes;
use Exception;
use RdKafka\Conf;
use RdKafka\KafkaConsumer as Consumer;
use RdKafka\Message;

trait ConsumerFeatures {
    use CommonFeatures;

    private int $timeout = 5000;

    protected string $inputTopic;
    protected string $groupID;
    protected Conf $config;
    
    protected function initConsumer() {
        if (!isset($this->inputTopic)) {
            $this->error("'inputTopic' property is not set!");
            exit(0);
        }

        if (!isset($this->groupID)) {
            $this->error("'groupID' property is not set!");
            exit(0);
        }
        
        $this->config = $this->getConsumerConfig($this->groupID);

        if ($this->verbose > 1) {
            $this->infoGraylog("Going to read from '" . $this->inputTopic . "' in group '" . $this->groupID, GraylogTypes::INFO);
        }
        $this->startSubscribe();
    }

    protected function startSubscribe() {
        $consumer = new Consumer($this->config);
        
        try {
            $consumer->subscribe([$this->inputTopic]);
        } catch (Exception $e) {
            $this->errorGraylog("Something wrong with consumer subscription", $e, ["inputTopic" => $this->inputTopic]);
        }

        try {
            while (true) {
                $message = $consumer->consume($this->timeout);
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $this->infoGraylog("Consuming", GraylogTypes::CONSUMED, $message);
                        
                        $this->handleKafkaRead($message);
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        $this->infoGraylog('No more messages; will wait for more...', GraylogTypes::NO_DATA);
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        $this->infoGraylog('Timed out!', GraylogTypes::WAITING);
                        break;
                    default:
                        throw new Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (Exception $e) {
            $this->errorGraylog("Something wrong when consuming from: " . $this->inputTopic, $e);
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}