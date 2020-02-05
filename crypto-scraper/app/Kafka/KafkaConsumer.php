<?php


namespace App\Kafka;

use Illuminate\Console\Command;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\KafkaConsumer as Consumer;
use Exception;

abstract class KafkaConsumer extends Command {
    private string $groupID;
    private string $broker = "kafka";
    private string $topic;
    private int $timeout = 2000;

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $groupID = $this->argument("groupID");
        $topic = $this->argument("topic");
        
        $this->groupID = $groupID;
        $this->topic = $topic;
        
        $conf = new Conf();
        $conf->set('group.id', $this->groupID);
        $conf->set('metadata.broker.list', $this->broker);
        $conf->set('auto.offset.reset', 'smallest'); // start from the beginning

        $consumer = new Consumer($conf);
        
        try {
            $consumer->subscribe([$this->topic]);
        } catch (Exception $e) {
            print "Something wrong with consumer subscription: " . $e->getMessage();
        }
        
        try {
            print "Going to read from '" . $this->topic . "' in group '" . $this->groupID . "'\n"; 
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
                        throw new \Exception($message->errstr(), $message->err);
                        break;
                }
            }
        } catch (Exception $e) {
            print "Something wrong then consuming from: " . $this->topic . "\n";
            print $e->getMessage();
        }
    }
    
    abstract protected function handleKafkaRead(Message $message);
}