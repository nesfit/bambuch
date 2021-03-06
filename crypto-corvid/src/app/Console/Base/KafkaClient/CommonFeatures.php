<?php
declare(strict_types=1);

namespace App\Console\Base\KafkaClient;

use RdKafka\Conf;

trait CommonFeatures {
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