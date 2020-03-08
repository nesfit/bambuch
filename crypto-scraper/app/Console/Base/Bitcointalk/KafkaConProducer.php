<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;

use App\Console\Base\Common\StoreCrawledUrl;
use App\Kafka\ConProducerFeatures;
use App\Models\KafkaUrlMessage;
use RdKafka\Message;

abstract class KafkaConProducer extends BitcointalkParser {
    use ConProducerFeatures;
    use StoreCrawledUrl;

    public function handle() {
        parent::handle();

        $this->initConProducer();
    }

    protected function processInputUrl(string $mainUrl) {
        $urls = $this->loadDataFromUrl($mainUrl);
        $count = count($urls);
        foreach ($urls as $num => $url) {
            $outUrlMessage = new KafkaUrlMessage($mainUrl, $url, $num === $count - 1);
            $this->storeChildUrl($outUrlMessage);
            $this->kafkaProduce($outUrlMessage->encodeData());
        }
    }
    
    protected function handleKafkaRead(Message $message) {
        $inUrlMessage = KafkaUrlMessage::decodeData($message->payload);
        $mainUrl = $inUrlMessage->url;

        if ($this->validateInputUrl($mainUrl)) {
            $this->processInputUrl($mainUrl);
            return 0;
        } else {
            $this->warningGraylog('Invalid main url', $mainUrl);
            return 1;
        }
    }
    
    abstract protected function validateInputUrl(string $url);
    abstract protected function loadDataFromUrl(string $url): array;
}