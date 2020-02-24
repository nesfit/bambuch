<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\UrlKeeper;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\BoardPage;
use App\Models\Pg\Bitcointalk\MainTopic;

//docker-compose -f common.yml -f dev.yml -f graylog.yml run --rm test bitcointalk:main_topics_keeper

class MainTopicsKeeper extends UrlKeeper
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::MAIN_TOPICS_KEEPER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store main topics into PG.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(MainTopic::class, BoardPage::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->inputTopic = BitcointalkKafka::MAIN_BOARDS_TOPIC;
        $this->groupID = BitcointalkKafka::MAIN_TOPICS_STORE_GROUP;
        $this->serviceName = self::MAIN_TOPICS_KEEPER;

        return parent::handle();
    }
}
