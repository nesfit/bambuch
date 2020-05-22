<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Producers;

use App\Console\Base\Bitcointalk\KafkaProducer;
use App\Console\Base\Common\GraylogTypes;
use App\Console\Base\Bitcointalk\UrlCalculations;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use App\Models\Pg\Bitcointalk\MainBoard;

class MainBoardsProducer extends KafkaProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'board';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::MAIN_BOARDS_PRODUCER .' {verbose=1} {url='. BitcointalkCommands::BITCOINTALK_URL .'} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::MAIN_BOARDS_PRODUCER_DESC;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->outputTopic = BitcointalkKafka::MAIN_BOARDS_TOPIC;
        $this->serviceName = BitcointalkCommands::MAIN_BOARDS_PRODUCER;
        $this->tableName = MainBoard::class;

        parent::handle();

        /**
         * All main boards are loaded in DB.
         * Send all into Kafka => enable re-scraping in order to get new board pages.
         */
        $allMainBoards = MainBoard::getAll();
        foreach ($allMainBoards as $mainBoard) {
            $urlMessage = new UrlMessage("empty", $mainBoard[BitcointalkModel::COL_URL], false);
            $this->kafkaProduce($urlMessage->encodeData());
        }
        
        /**
         * set all to unparsed
         * get unparsed from DB
         * get all from url
         * subtract the arrays
         * insert the result
         */
        MainBoard::setParsedToAll(false);
        $this->infoGraylog("Setting all to unparsed", GraylogTypes::INFO);
        $this->loadMainBoardsFromUrl($this->url);
        $this->loadChildMainBoards();
        
        return 0;
    }
    
    private function loadMainBoardsFromUrl(string $url) {
        if (self::mainBoardValid($url)) {
            $mainBoards = $this->getNewData($url);
            if ($mainBoards) {
                foreach ($mainBoards as $mainBoard) {
                    $urlMessage = new UrlMessage("empty", $mainBoard, false);
                    $this->storeMainUrl($urlMessage);
                    $this->kafkaProduce($urlMessage->encodeData());
                }
            } else {
                $this->infoGraylog("No new main boards", GraylogTypes::NO_DATA);
            }
            
            return 0;
        }
        else {
            $this->warningGraylog('Invalid main board url', ["url" => $url]);
            return 1;
        }
    }
    
    private function loadChildMainBoards() {
        $firstUnparsed = MainBoard::getFirstUnparsed();
        if ($firstUnparsed) {
            $unparsedUrl = $firstUnparsed->getAttribute(MainBoard::COL_URL);
            $this->loadMainBoardsFromUrl($unparsedUrl);

            $firstUnparsed->setAttribute(MainBoard::COL_PARSED, true);
            $firstUnparsed->save();
            
            unset($firstUnparsed);
            $this->loadChildMainBoards();
        }
    }

    protected function loadDataFromUrl(string $url): array {
        $allBoards = $this->getLinksFromPage($url, self::ENTITY);
        return self::getMainBoards($allBoards);
    }

    public static function getMainBoards(array $allBoards): array {
        return self::getMainEntity(self::ENTITY, $allBoards);
    }

    public static function mainBoardValid(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }
}
