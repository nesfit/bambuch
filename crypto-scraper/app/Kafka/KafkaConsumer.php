<?php
declare(strict_types=1);

namespace App\Kafka;

use RdKafka\Message;
use RdKafka\KafkaConsumer as Consumer;
use Exception;

abstract class KafkaConsumer extends KafkaCommon {
    private int $timeout = 2000;
    private string $groupID;
    private string $inputTopic;

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        parent::handle();
        $this->inputTopic = $this->argument("inputTopic");
        $this->groupID = $this->argument("groupID");
        
        $this->config->set('auto.offset.reset', 'smallest'); // start from the beginning
        $this->config->set('group.id', $this->groupID);
        
        $consumer = new Consumer($this->config);
        
        try {
            $consumer->subscribe([$this->inputTopic]);
        } catch (Exception $e) {
            print "Something wrong with consumer subscription: " . $e->getMessage();
        }
        
        try {
            print "Going to read from '" . $this->inputTopic . "' in group '" . $this->groupID . "'\n"; 
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
            print "Something wrong then consuming from: " . $this->inputTopic . "\n";
            print $e->getMessage();
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}