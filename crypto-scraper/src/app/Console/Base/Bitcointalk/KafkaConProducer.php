<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Kafka\ConProducerFeatures;
use App\Models\Kafka\UrlMessage;
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
            $outUrlMessage = new UrlMessage($mainUrl, $url, $num === $count - 1);
            $this->storeChildUrl($outUrlMessage);
            $this->kafkaProduce($outUrlMessage->encodeData());
        }
    }
    
    protected function handleKafkaRead(Message $message) {
        $inUrlMessage = UrlMessage::decodeData($message->payload);
        $mainUrl = $inUrlMessage->url;

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