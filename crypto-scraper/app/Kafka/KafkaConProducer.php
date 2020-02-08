<?php
declare(strict_types=1);

namespace App\Kafka;

abstract class KafkaConProducer extends KafkaProducer {
    use ConsumerFeatures;

    protected function handle() {
        parent::handle();
        $inputTopic = $this->argument("inputTopic");
        $groupID = $this->argument("groupID");

        $config = $this->getConsumerConfig($groupID);

        print "Going to read from '" . $inputTopic . "' in group '" . $groupID . "'\n";

        $this->startSubscribe($config, $inputTopic);
    }
}