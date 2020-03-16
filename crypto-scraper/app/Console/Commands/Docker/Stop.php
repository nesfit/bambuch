<?php
declare(strict_types=1);

namespace App\Console\Commands\Docker;

use App\Console\Base\Common\Maintenance;

class Stop extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::STOP;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop Kafka and Graylog';
    
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
        $this->callModule(self::KAFKA_STOP);
        $this->callModule(self::GRAYLOG_STOP);
        $this->callModule(self::POSTGRES_STOP);
        $this->callModule(self::LENSES_STOP);
        return 0;
    }
}
