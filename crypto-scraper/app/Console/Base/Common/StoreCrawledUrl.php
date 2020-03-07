<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Models\KafkaUrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkQueries;

trait StoreCrawledUrl {
    protected string $tableName;
    
    private function checkTable() {
        if (!isset($this->tableName)) {
            $this->errorGraylog("'tableName' property is not set!");
            exit(0);
        }        
    }
    
    protected function storeMainUrl(KafkaUrlMessage $message) {
        $this->checkTable();

        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkQueries::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkQueries::COL_PARSED, false);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }
    
    protected function storeChildUrl(KafkaUrlMessage $message) {
        $this->checkTable();

        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkQueries::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkQueries::COL_PARSED, false);
        $entity->setAttribute(BitcointalkQueries::COL_PARENT_URL, $message->mainUrl);
        $entity->setAttribute(BitcointalkQueries::COL_LAST, $message->last);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }
}