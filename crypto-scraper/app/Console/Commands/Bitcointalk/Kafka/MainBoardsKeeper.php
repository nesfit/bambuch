<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\MainUrlKeeper;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\MainBoard;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:main_boards_keeper

class MainBoardsKeeper extends MainUrlKeeper
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::MAIN_BOARDS_KEEPER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store main boards into PG.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(MainBoard::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->inputTopic = BitcointalkKafka::MAIN_BOARDS_TOPIC;
        $this->groupID = BitcointalkKafka::MAIN_BOARDS_STORE_GROUP;
        $this->serviceName = self::MAIN_BOARDS_KEEPER;

        return parent::handle();
    }
}
