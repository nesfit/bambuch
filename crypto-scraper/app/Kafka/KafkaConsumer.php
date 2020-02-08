<?php
declare(strict_types=1);

namespace App\Kafka;

use RdKafka\KafkaConsumer as Consumer;

abstract class KafkaConsumer extends KafkaCommon {
    use ConsumerFeatures;

    private string $groupID;
    private string $inputTopic;

    public function __construct() {
        parent::__construct();
    }

    protected function handle() {
        $this->inputTopic = $this->argument("inputTopic");
        $this->groupID = $this->argument("groupID");
        
        $config = $this->getConsumerConfig($this->groupID);

        print "Going to read from '" . $this->inputTopic . "' in group '" . $this->groupID . "'\n";
        
        $this->startSubscribe($config, $this->inputTopic);
    }
}