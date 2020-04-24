<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\KafkaClient\ConsumerFeatures;

abstract class KafkaConsumer extends BitcointalkParser {
    use ConsumerFeatures;

    public function handle() {
        parent::handle();

        $this->initConsumer();
    }
}