<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Producers;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Bitcointalk\UrlCalculations;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use App\Models\Pg\Bitcointalk\BoardPage;

class BoardPagesProducer extends KafkaConProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'board';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::BOARD_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::BOARD_PAGES_PRODUCER_DESC;

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
        $this->inputTopic = BitcointalkKafka::MAIN_BOARDS_TOPIC;
        $this->outputTopic = BitcointalkKafka::BOARD_PAGES_TOPIC;
        $this->groupID = BitcointalkKafka::MAIN_BOARDS_LOAD_GROUP;
        $this->serviceName = BitcointalkCommands::BOARD_PAGES_PRODUCER;
        $this->tableName = BoardPage::class;
    
        parent::handle();
        
        return 1;
    }

    protected function loadDataFromUrl(string $url): array {
        $maxBoardPage = $this->getMaxPage($url);
        if ($maxBoardPage) {
            $mainBoardId = self::getMainBoardId($url);
            $fromBoardId = self::getBoardPageId($url);
            $toBoardId = self::getBoardPageId($maxBoardPage);

            /**
             * Last board page has to be re-scraped again for possible new main topics.
             */
            $lastBoardPage = BoardPage::getLast($url);
            // TODO should always get one => LOG if not
            if ($lastBoardPage) {
                BoardPage::unparseLast($url);
                $lastUrl = $lastBoardPage->getAttribute(BitcointalkModel::COL_URL);
                $outUrlMessage = new UrlMessage($url, $lastUrl, true);
                $this->kafkaProduce($outUrlMessage->encodeData());
            }
            
            return self::calculateBoardPages($mainBoardId, $fromBoardId, $toBoardId);
        }
        return [$url];
    }

    protected function validateInputUrl(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }

    public static function getBoardPageId(string $url): ?int {
        return self::getEntityPageId(self::ENTITY, $url);
    }

    public static function getMainBoardId(string $url): ?int {
        return self::getMainEntityId(self::ENTITY, $url);
    }

    public static function calculateBoardPages(int $boardId, int $from, int $to): array {
        return self::calculateEntityPages(self::ENTITY, $boardId, $from, $to, 40);
    }
}
