<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaProducer;
use App\Console\Base\Common\StoreCrawledUrl;
use App\Console\Commands\Bitcointalk\Loaders\UrlCalculations;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkCommands;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\MainBoard;

//docker-compose -f common.yml -f dev.yml run --rm test bct:main_boards_producer

class MainBoardsProducer extends KafkaProducer {
    use UrlValidations;
    use UrlCalculations;
    use StoreCrawledUrl;

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
    protected $description = 'Send main boards into kafka';

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
         * get unparsed from DB
         * get all from url
         * subtract the arrays
         * insert the result
         */
        $this->loadMainBoardsFromUrl($this->url);
        $this->loadChildMainBoards();
        
        return 0;
    }
    
    private function loadMainBoardsFromUrl(string $url) {
        if (self::mainBoardValid($url)) {
            $mainBoards = $this->getNewData($url);
            foreach ($mainBoards as $mainBoard) {
                $urlMessage = new UrlMessage("empty", $mainBoard, false);
                $this->storeMainUrl($urlMessage);
                $this->kafkaProduce($urlMessage->encodeData());
            }
            
            return 0;
        }
        else {
            $this->warningGraylog('Invalid main board url', $url);
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
