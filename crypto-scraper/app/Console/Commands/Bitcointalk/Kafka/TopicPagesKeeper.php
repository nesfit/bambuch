<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\UrlKeeper;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\TopicPage;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:topic_pages_keeper

class TopicPagesKeeper extends UrlKeeper
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::TOPIC_PAGES_KEEPER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store topic pages into PG.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(TopicPage::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->inputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->groupID = BitcointalkKafka::TOPIC_PAGES_STORE_GROUP;
        $this->serviceName = self::MAIN_TOPICS_KEEPER;

        return parent::handle();
    }
}
