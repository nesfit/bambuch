<?php
declare(strict_types=1);

namespace App\Kafka;

abstract class KafkaConsumer extends KafkaCommon {
    use ConsumerFeatures;

    public function __construct() {
        parent::__construct();
    }

    protected function handle() {
        $this->inputTopic = $this->argument("inputTopic");
        $groupID = $this->argument("groupID");
        
        $this->config = $this->getConsumerConfig($groupID);

        print "Going to read from '" . $this->inputTopic . "' in group '" . $groupID . "'\n";
        
        $this->startSubscribe();
    }
}