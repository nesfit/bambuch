<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\Maintenance;

class GraylogStart extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::GRAYLOG_START;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run graylog';
    
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
        $this->startModule("graylog");
        return 0;
    }
}
