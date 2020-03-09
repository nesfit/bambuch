<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Kafka\ConsumerFeatures;

abstract class KafkaConsumer extends CryptoParser {
    use ConsumerFeatures;

    public function handle() {
        $this->initConsumer();
    }
}