<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Kafka\ConsumerFeatures;
use Illuminate\Console\Command;

abstract class KafkaConsumer extends Command {
    use ConsumerFeatures;

    protected function handle() {
        $this->inputTopic = $this->argument("inputTopic");
        $this->groupID = $this->argument("groupID");
        
        $this->initConsumer();
    }
}