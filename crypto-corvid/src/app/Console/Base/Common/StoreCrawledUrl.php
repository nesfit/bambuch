<?php
declare(strict_types=1);

namespace App\Console\Base\Common;

use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;

trait StoreCrawledUrl {
    protected string $tableName;
    
    private function checkTable() {
        if (!isset($this->tableName)) {
            $this->errorGraylog("'tableName' property is not set!");
            exit(0);
        }        
    }
    
    protected function storeMainUrl(UrlMessage $message) {
        $this->checkTable();

        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkModel::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkModel::COL_PARSED, false);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }
    
    protected function storeChildUrl(UrlMessage $message) {
        $this->checkTable();

        $entity = new $this->tableName();
        $entity->setAttribute(BitcointalkModel::COL_URL, $message->url);
        $entity->setAttribute(BitcointalkModel::COL_PARSED, false);
        $entity->setAttribute(BitcointalkModel::COL_PARENT_URL, $message->mainUrl);
        $entity->setAttribute(BitcointalkModel::COL_LAST, $message->last);
        $entity->save();

        $this->infoGraylog("Url stored", GraylogTypes::STORED, $message->url);
    }
}