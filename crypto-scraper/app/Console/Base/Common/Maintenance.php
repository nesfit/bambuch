<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

abstract class Maintenance extends Command {
    const START = "start";
    const STOP = "stop";
    const KAFKA_START = "kafka:start";
    const KAFKA_STOP = "kafka:stop";
    const GRAYLOG_START = "graylog:start";
    const GRAYLOG_STOP = "graylog:stop";
    const POSTGRES_START = "postgres:start";
    const POSTGRES_STOP = "postgres:stop";

    const STOP_ARGS = ["docker-compose", "-f", "common.yml", "stop"];
    const START_ARGS = ["docker-compose", "-f", "common.yml", "up", "-d"];

    
    private function maintainModule(array $args, string $module) {
        $process = new Process(array_merge($args, [$module]));
        $process->start();

        sleep(2);
    }

    protected function callModule(string $module) {
        print "Maintaining module: " . $module . "\n";
        $this->call($module);

        sleep(2);
    }
    
    protected function stopModule(string $module) {
        print "Stopping: " . $module . "\n";
        $this->maintainModule(self::STOP_ARGS, $module);
    }

    protected function startModule(string $module) {
        print "Starting: " . $module . "\n";
        $this->maintainModule(self::START_ARGS, $module);
    }
}