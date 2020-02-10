<?php
declare(strict_types=1);

namespace App\Console\Commands\Tests\KafkaContinuous;

class Kafka {
    const TEST_INPUT_TOPIC = "testInput";   
    const TEST_INPUT_GROUP = "testInputGroup";

    const TEST_OUTPUT_TOPIC = "testOutputTopic";
    const TEST_OUTPUT_GROUP = "testOutputGroup";
}