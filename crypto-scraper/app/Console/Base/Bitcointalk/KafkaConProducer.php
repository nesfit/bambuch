<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Kafka\ConProducerFeatures;

abstract class KafkaConProducer extends BitcointalkParser {
    use ConProducerFeatures;

    public function handle() {
        parent::handle();

        $this->initConProducer();
    }
}