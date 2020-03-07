<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\Common\GraylogTypes;
use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;
use RdKafka\Message;


abstract class UrlKeeper extends KafkaConsumer {
    private BitcointalkQueries $table;
    private string $className;
    
    public function __construct(string $className) {
        parent::__construct();
        
        $this->table = new $className();
        $this->className = $className;
    }
    
    public function handle() {
        $this->infoGraylog("Gonna store url into table", GraylogTypes::INFO, $this->table->getTableName());

        parent::handle();
        
        return 1;
    }
    
    public function handleKafkaRead(Message $message) {
        $urlMessage = KafkaUrlMessage::decodeData($message->payload);

        if (!$this->table::exists($urlMessage->url)) {
            $entity = new $this->className();
            $entity->setAttribute(BitcointalkQueries::COL_URL, $urlMessage->url);
            $entity->setAttribute(BitcointalkQueries::COL_PARSED, false);
            $entity->setAttribute(BitcointalkQueries::COL_PARENT_URL, $urlMessage->mainUrl);
            $entity->setAttribute(BitcointalkQueries::COL_LAST, $urlMessage->last);
            $entity->save();
            
            $this->infoGraylog("Url stored", GraylogTypes::STORED, $urlMessage->url);
        } else {
            $this->debugGraylog("Url already exists", GraylogTypes::INFO, $urlMessage->url);
        }
    }
}
