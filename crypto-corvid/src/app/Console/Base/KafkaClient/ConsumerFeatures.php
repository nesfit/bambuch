<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use App\Console\Base\Common\GraylogTypes;
use App\Console\Base\KafkaClient\CommonFeatures;
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
            $this->errorGraylog("'inputTopic' property is not set!");
            exit(0);
        }

        if (!isset($this->groupID)) {
            $this->errorGraylog("'groupID' property is not set!");
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
                        
                        try {
                            $this->infoGraylog(
                                "PHP memory allocation - before handleKafkaRead",
                                GraylogTypes::INFO,
                                memory_get_usage(),
                                [
                                    "trueAlloc" => memory_get_usage(true),
                                    "percentage" => memory_get_usage(true) / 1004544000
                                ]
                            );
                            $this->handleKafkaRead($message);
                            $this->infoGraylog(
                                "PHP memory allocation - after handleKafkaRead",
                                GraylogTypes::INFO,
                                memory_get_usage(),
                                [
                                    "trueAlloc" => memory_get_usage(true),
                                    "percentage" => memory_get_usage(true) / 1004544000
                                ]
                            );
                        } catch (Exception $e) {
                            $this->errorGraylog("Couldn't handleKafkaRead", $e);
                        }
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        $this->infoGraylog('No more messages; will wait for more...', GraylogTypes::NO_DATA);
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        $this->infoGraylog('Timed out!', GraylogTypes::WAITING);
                        break;
                    default:
                        $this->errorGraylog("Unknown consuming error", new Exception($message->errstr(), $message->err));
                        break;
                }
            }
        } catch (Exception $e) {
            $this->errorGraylog("Something wrong when consuming from: " . $this->inputTopic, $e);
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}