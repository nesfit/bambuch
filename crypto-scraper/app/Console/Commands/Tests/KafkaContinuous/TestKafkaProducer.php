<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

use App\Kafka\KafkaProducer;

class TestKafkaProducer extends KafkaProducer {

    protected $signature = 'producer:test {outputTopic}';
    protected $description = 'Testing kafka producer';

    public function __construct() {
        parent::__construct();
    }
    
    public function handle() {
        parent::handle();
        
        for ($i = 0; $i < 100; $i++) {
//            print "producing... \n";
            $this->kafkaProduce(strval($i));
            sleep(1);
        }
    }
}