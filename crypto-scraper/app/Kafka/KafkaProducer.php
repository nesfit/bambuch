<?php


namespace App\Kafka;

use Illuminate\Console\Command;
use RdKafka\Conf;
use RdKafka\Producer;
use RdKafka\ProducerTopic;

abstract class KafkaProducer extends Command {
    private string $broker = "kafka";
    private string $debug = "generic";
    private Producer $producer;
    private ProducerTopic $topic;

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $topicName = $this->argument("topicName");
        
        $conf = new Conf();
        $conf->set('debug',$this->debug);
        $conf->set('metadata.broker.list', $this->broker);

        $this->producer = new Producer($conf);

        $this->topic = $this->producer->newTopic($topicName);

        return 1;
    }
    
    protected function produce(string $message) {

        $this->topic->produce(0, 0, $message);
        $result = $this->producer->flush(10000);
        
        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }
    } 
}