<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use App\Console\Base\KafkaClient\ConProducerFeatures;
use Illuminate\Console\Command;

abstract class KafkaConProducer extends Command {
    use ConProducerFeatures;

    protected function handle() {        
        $this->initConProducer();
    }
}