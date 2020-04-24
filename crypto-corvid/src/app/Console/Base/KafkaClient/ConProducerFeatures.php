<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use App\Console\Base\KafkaClient\ConsumerFeatures;
use App\Console\Base\KafkaClient\ProducerFeatures;

trait ConProducerFeatures {
    use ConsumerFeatures;
    use ProducerFeatures;

    protected function initConProducer() {
        $this->initProducer();
        $this->initConsumer();
    }
}