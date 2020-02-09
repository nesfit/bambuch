<?php

namespace Tests\Feature;

use App\Kafka\KafkaProducer;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KafkaConsumersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function disabledTestProducer() {
//        $this->artisan("producer:test_single", ["outputTopic" => "singleOutputTopic"])
//            ->expectsOutput("Original message");
        $params = array("singleOutputTopic");
//        Artisan::call("producer:test_single", ["outputTopic" => "singleOutputTopic"]);
        Artisan::call("producer:test_single", para);
    }
    
}
