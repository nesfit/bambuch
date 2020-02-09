<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaSingle;

use App\Kafka\KafkaProducer;

class TestKafkaProducer extends KafkaProducer {

    protected $signature = 'producer:test_single {outputTopic}';
    protected $description = 'Testing kafka producer';

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
        
        $message = "Original message";
        print $message . "\n";
        $this->kafkaProduce($message);
        return 1;
    }
}