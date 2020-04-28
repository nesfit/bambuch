<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Producers;

use App\Console\Base\Bitcointalk\KafkaConProducer;
use App\Console\Base\Bitcointalk\UrlCalculations;
use App\Console\Base\Bitcointalk\UrlValidations;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use App\Console\Constants\Bitcointalk\BitcointalkKafka;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\UserProfile;

class UserProfilesProducer extends KafkaConProducer {
    use UrlValidations;
    use UrlCalculations;

    const ENTITY = 'topic';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = BitcointalkCommands::USER_PROFILES_PRODUCER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = BitcointalkCommands::USER_PROFILES_PRODUCER_DESC;

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
        $this->inputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->outputTopic = BitcointalkKafka::USER_PROFILES_TOPIC;
        $this->groupID = BitcointalkKafka::TOPIC_PAGES_PROFILE_GROUP;
        $this->serviceName = BitcointalkCommands::USER_PROFILES_PRODUCER;
        $this->tableName = UserProfile::class;

        parent::handle();

        return 1;
    }

    protected function validateInputUrl(string $url) {
        return self::pageEntityValid(self::ENTITY, $url);
    }
    
    protected function loadDataFromUrl(string $url): array {
        return $this->getLinksFromPage($url, 'action=profile');
    }

    // TODO merge with StoreCrawledUrl
    protected function storeChildUrl(UrlMessage $message) {
        $topicPage = new UserProfile();
        $topicPage->setAttribute(UserProfile::COL_URL, $message->url);
        $topicPage->setAttribute(UserProfile::COL_PARSED, false);
        $topicPage->save();
    }
}
