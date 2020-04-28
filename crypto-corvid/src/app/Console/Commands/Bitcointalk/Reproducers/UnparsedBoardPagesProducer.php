<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\UnparsedLoaders;

use App\Console\Base\Bitcointalk\UnparsedProducer;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\BoardPage;

class UnparsedBoardPagesProducer extends UnparsedProducer {

    protected $signature = BitcointalkCommands::UN_BOARD_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';
    
    protected $description = BitcointalkCommands::UN_BOARD_PAGES_PRODUCER_DESC;
    
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->outputTopic = BitcointalkKafka::BOARD_PAGES_TOPIC;
        $this->serviceName = BitcointalkCommands::UN_BOARD_PAGES_PRODUCER;
        $this->tableName = BoardPage::class;

        parent::handle();
    }
}