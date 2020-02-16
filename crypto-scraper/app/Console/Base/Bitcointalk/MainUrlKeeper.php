<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;
use RdKafka\Message;


abstract class MainUrlKeeper extends KafkaConsumer {
    protected BitcointalkQueries $table;
    
    public function __construct(BitcointalkQueries $table) {
        parent::__construct();
        
        $this->table = $table;
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
        
        if (!$this->table::exists($urlMessage->url)) {
            $this->table->setAttribute($this->table::COL_URL, $urlMessage->url);
            $this->table->setAttribute($this->table::COL_PARSED, false);
            $this->table->save();
        }
    }
}
