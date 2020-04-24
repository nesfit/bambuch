<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\KafkaClient\ProducerFeatures;

abstract class KafkaProducer extends BitcointalkParser {
    use ProducerFeatures;

    public function handle() {
        parent::handle();

        $this->initProducer();
    }
}