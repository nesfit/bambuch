<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Kafka\ConsumerFeatures;

abstract class KafkaConsumer extends BitcointalkParser {
    use ConsumerFeatures;

    public function handle() {
        parent::handle();
        $this->inputTopic = $this->argument("inputTopic");
        $this->groupID = $this->argument("groupID");

        $this->initConsumer();
    }
}