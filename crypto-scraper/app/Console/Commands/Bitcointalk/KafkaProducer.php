<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Kafka\ProducerFeatures;

abstract class KafkaProducer extends BitcointalkParser {
    use ProducerFeatures;

    public function handle() {
        parent::handle();
        $this->outputTopic = $this->argument("outputTopic");

        $this->initProducer();
    }
}