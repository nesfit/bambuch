<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk\Kafka;

use App\Console\Base\Bitcointalk\KafkaConsumer;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;
use RdKafka\Message;


abstract class UrlKeeper extends KafkaConsumer {
    protected BitcointalkQueries $table;
    
    public function __construct(BitcointalkQueries $table) {
        parent::__construct();
        
        $this->table = $table;
        print "Gonna store url into table: " . $table->getTableName() . "\n";
    }
    
    public function handle() {        
        parent::handle();
        
        return 1;
    }
    
    public function handleKafkaRead(Message $message) {
        $urlToStore = $message->payload;

        if (!$this->table::exists($urlToStore)) {
            $this->table->setAttribute($this->table::COL_URL, $urlToStore);
            $this->table->setAttribute($this->table::COL_PARSED, false);
//            $this->table->setAttribute($this->table::COL_MAIN_BOARD, $mainBoardId);
//            $this->table->setAttribute($this->table::COL_LAST, $key === $pagesCount - 1);
            $this->table->save();
        }
    }
}
