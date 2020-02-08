<?php
declare(strict_types=1);


namespace App\Kafka;

use Illuminate\Console\Command;
use RdKafka\Conf;


class KafkaCommon extends Command {
    private string $broker = "kafka";
//    private string $debug = "queue";
//    private string $debug = "topic";
//    private string $debug = "generic";
    protected Conf $config;
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function handle() {
        $this->config = new Conf();
//        $this->config->set('debug', $this->debug);
        $this->config->set('metadata.broker.list', $this->broker);
    }
}