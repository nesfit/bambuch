<?php
declare(strict_types=1);

namespace App\Kafka;

use Exception;
use RdKafka\Conf;
use RdKafka\KafkaConsumer as Consumer;
use RdKafka\Message;

trait ConsumerFeatures {
    private int $timeout = 5000;

    protected function startSubscribe(Conf $config, string $inputTopic) {
        $consumer = new Consumer($config);
        
        try {
            $consumer->subscribe([$inputTopic]);
        } catch (Exception $e) {
            print "Something wrong with consumer subscription: " . $e->getMessage();
        }

        try {
            while (true) {
                $message = $consumer->consume($this->timeout);
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $this->handleKafkaRead($message);
                        break;
                    case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                        echo "No more messages; will wait for more\n";
                        break;
                    case RD_KAFKA_RESP_ERR__TIMED_OUT:
                        echo "Timed out\n";
                        break;
                    default:
                        throw new Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (Exception $e) {
            print "Something wrong then consuming from: " . $inputTopic . "\n";
            print $e->getMessage();
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}