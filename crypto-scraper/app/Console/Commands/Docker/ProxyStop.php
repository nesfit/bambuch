<?php
declare(strict_types=1);

namespace App\Console\Commands\Docker;

use App\Console\Base\Common\Maintenance;
use Symfony\Component\Process\Process;

class ProxyStop extends Maintenance {
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::PROXY_STOP;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop proxy';
    
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
        $this->stopModule("proxy");
        return 0;
    }
}
