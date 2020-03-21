<?php
declare(strict_types=1);

namespace App\Console\Commands\Docker;

use App\Console\Base\Common\Maintenance;

class Start extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::START;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Kafka and Graylog';
    
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
        $this->callModule(self::KAFKA_START);
        $this->callModule(self::GRAYLOG_START);
        $this->callModule(self::POSTGRES_START);
        $this->callModule(self::LENSES_START);
        $this->callModule(self::PROXY_START);
        $this->callModule(self::FETCH_WALLET_EXPLORER);
        return 0;
    }
}
