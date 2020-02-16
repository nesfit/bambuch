<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Commands\Bitcointalk\Loaders\UrlCalculations;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkKafka;
use App\Models\KafkaUrlMessage;
use RdKafka\Message;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:board_pages_producer

class BoardPagesProducer extends KafkaConProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'board';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::BOARD_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load board pages from main boards.';

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
    
        parent::handle();
        
        return 1;
    }

    protected function handleKafkaRead(Message $message) {
        $mainBoardUrl = $message->payload;
        if (self::mainBoardValid($mainBoardUrl)) {
            $boardPages = $this->loadBoardPages($mainBoardUrl);
            $pagesCount = count($boardPages);
            foreach ($boardPages as $num => $boardPage) {
                $urlMessage = new KafkaUrlMessage($mainBoardUrl, $boardPage, $num === $pagesCount - 1);
                $this->kafkaProduce($urlMessage->encodeData());
            }
            return 1;
        } else {
            $this->printRedLine('Invalid main board url: ' . $mainBoardUrl);
            return 0;
        }
    }

    private function loadBoardPages(string $url): array {
        $maxBoardPage = $this->getMaxPage($url);
        if ($maxBoardPage) {
            $mainBoardId = self::getMainBoardId($url);
            $fromBoardId = self::getBoardPageId($url);
            $toBoardId = self::getBoardPageId($maxBoardPage);

            return self::calculateBoardPages($mainBoardId, $fromBoardId, $toBoardId);
        }
        return [$url];
    }

    public static function getMainBoards(array $allBoards): array {
        return self::getMainEntity(self::ENTITY, $allBoards);
    }

    public static function mainBoardValid(string $url): bool {
        return self::mainEntityValid(self::ENTITY, $url);
    }

    public static function getBoardPages(array $allBoards): array {
        return self::getEntityPages(self::ENTITY, $allBoards);
    }

    public static function getBoardPageId(string $url): ?int {
        return self::getEntityPageId(self::ENTITY, $url);
    }

    public static function getMainBoardId(string $url): ?int {
        return self::getMainEntityId(self::ENTITY, $url);
    }

    public static function calculateBoardPages(int $boardId, int $from, int $to): array {
        return self::calculateEntityPages(self::ENTITY, $boardId, $from, $to);
    }
}
