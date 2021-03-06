<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use App\Console\Base\KafkaClient\ProducerFeatures;
use Illuminate\Console\Command;
use RdKafka\Producer;
use RdKafka\ProducerTopic;

abstract class KafkaProducer extends Command {
    use ProducerFeatures;
    
    private Producer $producer;
    private ProducerTopic $topic;

    protected function handle() {
        $this->initProducer();
    }
}