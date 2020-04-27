<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Console\Base\KafkaClient\KafkaProducer;

// docker-compose -f infra.yml -f backend.yml run --rm scraper producer:test

class TestKafkaProducer extends KafkaProducer {

    protected $signature = 'producer:test';
    protected $description = 'Testing kafka producer';

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        $this->outputTopic = Kafka::TEST_INPUT_TOPIC;
        
        parent::handle();
        
        for ($i = 0; $i < 100; $i++) {
            print "producing... \n";
            $this->kafkaProduce(strval($i));
            sleep(1);
        }
    }
}