<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaProducer;
use App\Console\Constants\BitcointalkKafka;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\TopicPage;

//docker-compose -f common.yml -f dev.yml run --rm test bitcointalk:topic_pages_producer

class TopicPagesProducer extends KafkaProducer
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::TOPIC_PAGES_PRODUCER .' {verbose=1} {--force} {dateTime?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send topics pages into Kafka';

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
        $this->outputTopic = BitcointalkKafka::TOPIC_PAGES_TOPIC;
        $this->serviceName = self::TOPIC_PAGES_PRODUCER;

        parent::handle();

        if($this->option("force")) {
            $this->printCyanLine("Force update!");
            TopicPage::setParsedToAll(false);
        }

//        $mainTopics = array_slice(TopicPage::getAllUnParsed(), 0, 5);
        $mainTopics = TopicPage::getAllUnParsed();
        $topicCount = count($mainTopics);
        if ($topicCount) {
            foreach ($mainTopics as $mainTopic) {
                $mainTopicUrl = $mainTopic->getAttribute(MainTopic::COL_URL);
                print "Sending message: " . $mainTopicUrl . "\n";
                $this->kafkaProduce($mainTopicUrl);
                sleep(3);
            }
        } else {
            $this->printRedLine("No unparsed topic pages found!");
            return 0;
        }
        return 1;
    }
}
