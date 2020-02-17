<?php
declare(strict_types=1);

namespace App\Kafka;

use Exception;
use Illuminate\Support\Facades\Log;
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
            print "Going to read from '" . $this->inputTopic . "' in group '" . $this->groupID . "'\n";
        }
        $this->startSubscribe();
    }

    protected function startSubscribe() {
        $consumer = new Consumer($this->config);
        
        try {
            $consumer->subscribe([$this->inputTopic]);
        } catch (Exception $e) {
            print "Something wrong with consumer subscription: " . $e->getMessage();
        }

        try {
            while (true) {
                $message = $consumer->consume($this->timeout);
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        if ($this->verbose > 1) {
                            print "Consumed message: " . $message->payload . "\n";
                        }
                        
                        $this->handleKafkaRead($message);
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        Log::info('No more messages; will wait for more.', ["serviceName" => $this->serviceName]);
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        Log::info('Timed out!', ["serviceName" => $this->serviceName]);
                        break;
                    default:
                        throw new Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (Exception $e) {
            $this->error("Something wrong then consuming from: " . $this->inputTopic);
            $this->error($e->getMessage());
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}