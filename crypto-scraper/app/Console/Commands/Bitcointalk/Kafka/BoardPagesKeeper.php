<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\UrlKeeper;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\BoardPage;
use App\Models\Pg\Bitcointalk\MainBoard;

//docker-compose -f common.yml -f dev.yml -f graylog.yml run --rm test bitcointalk:board_pages_keeper

class BoardPagesKeeper extends UrlKeeper
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::BOARD_PAGES_KEEPER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store board pages into PG.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(BoardPage::class, MainBoard::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->inputTopic = BitcointalkKafka::BOARD_PAGES_TOPIC;
        $this->groupID = BitcointalkKafka::BOARD_PAGES_STORE_GROUP;
        $this->serviceName = self::BOARD_PAGES_KEEPER;

        return parent::handle();
    }
}
