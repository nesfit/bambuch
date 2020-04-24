<?php
declare(strict_types=1);

namespace App\Console\Commands\Common;

use App\Console\Base\Common\GraylogTypes;
use App\Console\Base\Common\KafkaConsumer;
use App\Console\Base\Common\ReturnCodes;
use App\Console\Constants\Common\CommonCommands;
use App\Console\Constants\Common\CommonKafka;
use RdKafka\Message;

//docker-compose -f infra.yml -f backend.yml run --rm scraper scraped_results_consumer

class ScrapeConsumer extends KafkaConsumer {
    use ReturnCodes;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = CommonCommands::SCRAPED_RESULTS_CONSUMER . '{verbose=1} {--force} {dateTime?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store scraped information into PG DB.';

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
        $this->inputTopic = CommonKafka::SCRAPE_RESULTS_TOPIC;
        $this->groupID = CommonKafka::SCRAPE_RESULTS_GROUP;
        $this->serviceName = CommonCommands::SCRAPED_RESULTS_CONSUMER;
        
        parent::handle();
        
        return 1;
    }
    
    protected function handleKafkaRead(Message $message) {
        list($owner, $url, $label, $source, $address, $cryptoType, $category) = explode("\t", $message->payload); 
                
        $success = $this->call('insert:db', [
            'owner name' => $owner,
            'url' => $url,
            'label' => $label,
            'source' => $source,
            'address' => $address,
            'crypto type' => $cryptoType,
            'category' => $category
        ]);
        
        switch ($success) {
            case $this->RETURN_ALREADY_EXISTS:
                $this->infoGraylog("Already exists", GraylogTypes::EXISTS, $url);
                break;
            case $this->RETURN_FAILED:
                $this->errorGraylog("Insert failed");
                break;
            case $this->RETURN_NEW_IDENTITY:
                $this->infoGraylog("New identity", GraylogTypes::STORED, $url);
                break;
            case $this->RETURN_NEW_ADDRESS:
                $this->infoGraylog("New address", GraylogTypes::STORED, $url);
                break;            
        }
    }
}
