<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\KafkaClient\ConProducerFeatures;
use App\Models\Kafka\UrlMessage;
use App\Models\Pg\Bitcointalk\BitcointalkModel;
use RdKafka\Message;

abstract class KafkaConProducer extends BitcointalkParser {
    use ConProducerFeatures;
        
    public function handle() {
        parent::handle();

        $this->initConProducer();
    }

    protected function processInputUrl(string $mainUrl) {
        $urls = $this->getNewData($mainUrl);
        $count = count($urls);
        
        foreach ($urls as $num => $url) {
            $last = $num === $count - 1;
            if ($last) {
                /**
                 * @var $table BitcointalkModel
                 */
                $table = new $this->tableName();
                $table::unsetLast($mainUrl);
            }
            $outUrlMessage = new UrlMessage($mainUrl, $url, $last);
            $this->storeChildUrl($outUrlMessage);
            $this->kafkaProduce($outUrlMessage->encodeData());
        }            
    }
    
    protected function handleKafkaRead(Message $message): int {
        $inUrlMessage = UrlMessage::decodeData($message->payload);
        $mainUrl = $inUrlMessage->url;

        // TODO check if the scraped url IS UNPARSED => SHOULD BE!
        
        if ($this->validateInputUrl($mainUrl)) {
            $this->processInputUrl($mainUrl);
            return 0;
        } else {
            $this->warningGraylog('Invalid input url', $mainUrl);
            return 1;
        }
    }
    
    abstract protected function validateInputUrl(string $url);
}