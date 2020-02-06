<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Kafka\KafkaProducer;

class TestKafkaProducer extends KafkaProducer {

    protected $signature = 'producer:test {topicName}';
    protected $description = 'Testing kafka producer';

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
        
        for ($i = 0; $i < 100; $i++) {
            $this->produce(strval($i));
            sleep(2);
        }
    }
}