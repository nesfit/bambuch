<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaConsumer;
use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;
use RdKafka\Message;


abstract class UrlKeeper extends KafkaConsumer {
    protected BitcointalkQueries $table;
    protected BitcointalkQueries $mainTable;
    
    public function __construct(BitcointalkQueries $table, BitcointalkQueries $mainTable) {
        parent::__construct();
        
        $this->table = $table;
        $this->mainTable = $mainTable;
        if ($this->verbose > 1) {
            print "Gonna store url into table: " . $table->getTableName() . "\n";
        }
    }
    
    public function handle() {        
        parent::handle();
        
        return 1;
    }
    
    public function handleKafkaRead(Message $message) {
        $urlMessage = KafkaUrlMessage::decodeData($message->payload);
        
        var_dump($urlMessage);

        if (!$this->table::exists($urlMessage->url)) {
            $mainEntity = $this->mainTable::getByUrl($urlMessage->mainUrl);
            
            if ($mainEntity) {
                $mainId = $mainEntity->getAttribute(BitcointalkQueries::COL_ID);
                $this->table::unsetLast($mainId);
                
                $this->table->setAttribute($this->table::COL_URL, $urlMessage->url);
                $this->table->setAttribute($this->table::COL_PARSED, false);
                $this->table->setAttribute($this->mainTable, $mainId);
                $this->table->setAttribute($this->table::COL_LAST, $urlMessage->last);
                $this->table->save();
                
                if ($urlMessage->last) {
                    $mainEntity->setAttribute(BitcointalkQueries::COL_PARSED, true);
                    $mainEntity->save();
                }
            } else {
                $this->printRedLine('Main board not found: ' . $urlMessage->mainUrl);
            }
        }
    }
}
