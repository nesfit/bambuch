<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Kafka\ConProducerFeatures;
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