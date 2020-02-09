<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Kafka\ConProducerFeatures;

abstract class KafkaConProducer extends BitcointalkParser {
    use ConProducerFeatures;

    public function handle() {
        parent::handle();
        $this->inputTopic = $this->argument("inputTopic");
        $this->outputTopic = $this->argument("outputTopic");
        $this->groupID = $this->argument("groupID");
        
        $this->initConProducer();
    }
}