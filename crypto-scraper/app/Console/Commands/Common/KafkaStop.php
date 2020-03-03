<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\Maintenance;

class KafkaStop extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::KAFKA_STOP;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop kafka';
    
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
        $this->stopModule("kafka");
        $this->stopModule("zookeeper");
        return 0;
    }
}
