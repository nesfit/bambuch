<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaProducer;
use App\Console\Commands\Bitcointalk\Loaders\UrlCalculations;
use App\Console\Commands\Bitcointalk\UrlValidations;
use App\Console\Constants\BitcointalkKafka;
use App\Models\KafkaUrlMessage;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:main_boards_producer

class MainBoardsProducer extends KafkaProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'board';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::MAIN_BOARDS_PRODUCER .' {url='. self::BITCOINTALK_URL .'} {verbose=1} {--force} {dateTime?}';


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
        $this->serviceName = self::MAIN_BOARDS_PRODUCER;

        parent::handle();

        if (self::mainBoardValid($this->url)) {
            $mainBoards = $this->loadMainBoards($this->url);
            foreach ($mainBoards as $mainBoard) {
                $urlMessage = new KafkaUrlMessage("empty", $mainBoard, false);
                $this->kafkaProduce($urlMessage->encodeData());
            }
            return 1;
        }
        else {
            $this->printRedLine('Invalid main board url: ' . $this->url);
            return 0;
        }
    }

    private function loadMainBoards(string $url): array {
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
