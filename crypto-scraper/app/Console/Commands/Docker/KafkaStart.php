<?php
declare(strict_types=1);

namespace App\Console\Commands\Docker;

use App\Console\Base\Common\Maintenance;

class KafkaStart extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::KAFKA_START;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run kafka';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->startModule("kafka");
        return 0;
    }
}
