<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use App\Console\Base\Common\CryptoParser;
use App\Console\Base\KafkaClient\ConsumerFeatures;

abstract class KafkaConsumer extends CryptoParser {
    use ConsumerFeatures;

    public function handle() {
        $this->initConsumer();
    }
}