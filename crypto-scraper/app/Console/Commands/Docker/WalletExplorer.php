<?php
declare(strict_types=1);

namespace App\Console\Commands\Docker;

use App\Console\Base\Common\Maintenance;

class WalletExplorer extends Maintenance {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::FETCH_WALLET_EXPLORER;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch WE';
    
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
        $this->startModule("wallet-explorer");
        return 0;
    }
}
