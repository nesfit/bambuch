<?php

namespace App\Console\Commands\Bitcointalk;

use App\Console\BitcointalkParser;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\TopicPage;
use RdKafka\Conf;
use RdKafka\Producer;

class BoardProducer extends BitcointalkParser
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::BOARD_PRODUCER .' {verbose=1} {--force} {dateTime?}';


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
        parent::handle();

        if($this->option("force")) {
            $this->printCyanLine("Force update!");
            TopicPage::setParsedToAll(false);
        }


        $conf = new Conf();
        //$conf->set('debug','all');

        $producer = new Producer($conf);
//        $kafka->setLogLevel(LOG_DEBUG);
        $producer->addBrokers('kafka');

        $topic = $producer->newTopic("topic_pages");
        print "Getting started \n";

        $mainTopics = array_slice(TopicPage::getAllUnParsed(), 0, 5);
        $topicCount = count($mainTopics);
        if ($topicCount) {
            foreach ($mainTopics as $mainTopic) {
                print "Sending message... \n";
                $mainTopicUrl = $mainTopic->getAttribute(MainTopic::COL_URL);
                $topic->produce(0, 0, $mainTopicUrl);
                $producer->poll(0);
                sleep(1);
            }
//            return 1;
        } else {
            $this->printRedLine("No unparsed topic pages found!");
            return 0;
        }
        
        
//        for ($i = 0; $i < 10; $i++) {
//            $message = sprintf('Message %d', $i);
//            print sprintf('Producing: %s', $message);
//            $topic->produce(KAFKA_PARTITION, 0, $message);
//            $producer->poll(0);
//        }

        for ($flushRetries = 0; $flushRetries < $topicCount; $flushRetries++) {
            $result = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }

        return 1;
    }
}
