<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;
use RdKafka\Message;


abstract class UrlKeeper extends KafkaConsumer {
    private BitcointalkQueries $table;
    private BitcointalkQueries $mainTable;
    private string $className;
    
    public function __construct(string $className, string $mainClassName) {
        parent::__construct();
        
        $this->table = new $className();
        $this->mainTable = new $mainClassName();
        $this->className = $className;
    }
    
    public function handle() {
        $this->infoGraylog("Gonna store url into table", $this->table->getTableName());

        parent::handle();
        
        return 1;
    }
    
    public function handleKafkaRead(Message $message) {
        $urlMessage = KafkaUrlMessage::decodeData($message->payload);

        if (!$this->table::exists($urlMessage->url)) {
            $mainEntity = $this->mainTable::getByUrl($urlMessage->mainUrl);
            
            if ($mainEntity) {
                $mainId = $mainEntity->getAttribute(BitcointalkQueries::COL_ID);
                $this->table::unsetLast($mainId);
                
                $entity = new $this->className();
                $entity->setAttribute(BitcointalkQueries::COL_URL, $urlMessage->url);
                $entity->setAttribute(BitcointalkQueries::COL_PARSED, false);
                $entity->setAttribute(BitcointalkQueries::COL_PARENT_ID, $mainId);
                $entity->setAttribute(BitcointalkQueries::COL_LAST, $urlMessage->last);
                $entity->save();
                
                if ($urlMessage->last) {
                    $mainEntity->setAttribute(BitcointalkQueries::COL_PARSED, true);
                    $mainEntity->save();
                }
                $this->infoGraylog("Url stored", $urlMessage->url);
            } else {
                $this->warningGraylog('Main board not found', $urlMessage);
            }
        } else {
            $this->debugGraylog("Url already exists", $urlMessage->url);
        }
    }
}
