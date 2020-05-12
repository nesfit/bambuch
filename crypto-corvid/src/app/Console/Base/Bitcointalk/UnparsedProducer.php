<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;

abstract class UnparsedProducer extends KafkaProducer {

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        parent::handle();
        
        $this->loadUnparsed();
    }
    
    protected function loadUnparsed() {
        /**
         * @var $table BitcointalkModel
         */
        $table = new $this->tableName();
        
        $unparsedPages = $table::getAllUnParsed();
        foreach ($unparsedPages as $unparsedPage) {
            $parentUrl = $unparsedPage->getAttribute(BitcointalkModel::COL_PARENT_URL);
            $url = $unparsedPage->getAttribute(BitcointalkModel::COL_URL);
            $urlMessage = new UrlMessage($parentUrl ?? '', $url, false);
            $this->kafkaProduce($urlMessage->encodeData());
        }
    }

    protected function loadDataFromUrl(string $url): array {
        return [];
    }
}