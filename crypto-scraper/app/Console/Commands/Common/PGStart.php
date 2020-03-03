<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\Maintenance;
use Symfony\Component\Process\Process;

class PGStart extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::POSTGRES_START;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start DB';
    
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
        $this->startModule("db");
        return 0;
    }
}
