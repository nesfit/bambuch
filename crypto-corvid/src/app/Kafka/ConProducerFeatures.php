<?php
declare(strict_types=1);

namespace App\Kafka;

trait ConProducerFeatures {
    use ConsumerFeatures;
    use ProducerFeatures;

    protected function initConProducer() {
        $this->initProducer();
        $this->initConsumer();
    }
}