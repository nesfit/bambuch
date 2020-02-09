<?php
declare(strict_types=1);

namespace App\Kafka;

use Illuminate\Console\Command;

abstract class KafkaConProducer extends Command {
    use ConProducerFeatures;

    protected function handle() {
        $this->inputTopic = $this->argument("inputTopic");
        $this->outputTopic = $this->argument("outputTopic");
        $this->groupID = $this->argument("groupID");
        
        $this->initConProducer();
    }
}