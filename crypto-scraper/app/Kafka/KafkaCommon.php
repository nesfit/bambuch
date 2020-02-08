<?php
declare(strict_types=1);

namespace App\Kafka;

use Illuminate\Console\Command;
use RdKafka\Conf;

abstract class KafkaCommon extends Command {
    private string $broker = "kafka";
    
    private function getConfig(): Conf {
        $config = new Conf();
        $config->set('metadata.broker.list', $this->broker);
        return $config;
    }
    
    protected function getConsumerConfig(string $groupID): Conf {
        $config = $this->getConfig();
        $config->set('auto.offset.reset', 'smallest'); // start from the beginning
        $config->set('group.id', $groupID);
        return $config;
    }
    
    protected function getProducerConfig(): Conf {
        return $this->getConfig();
    }
    
    
}