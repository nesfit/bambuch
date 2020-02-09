<?php
declare(strict_types=1);

namespace App\Kafka;

use Illuminate\Console\Command;
use RdKafka\Producer;
use RdKafka\ProducerTopic;

abstract class KafkaProducer extends Command {
    use ProducerFeatures;
    
    private Producer $producer;
    private ProducerTopic $topic;
    private string $outputTopic;

    protected function handle() {
        $this->outputTopic = $this->argument("outputTopic");

        $this->initProducer();
    }
}