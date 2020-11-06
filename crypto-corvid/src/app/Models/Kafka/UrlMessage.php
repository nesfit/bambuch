<?php
declare(strict_types=1);

namespace App\Models\Kafka;

class UrlMessage implements KafkaMessage {
    public string $mainUrl;
    public string $url;
    public bool $last;
    
    public function __construct(string $mainUrl, string $url, bool $last) {
        $this->mainUrl = $mainUrl;
        $this->url = $url;
        $this->last = $last;
    }
    
    public function toJSON(): string {
        return json_encode([
            'mainUrl' => $this->mainUrl, 
            'url' => $this->url, 
            'last' => $this->last
        ]);
    }
    
    public static function fromJSON(string $json): UrlMessage {
        $decoded = json_decode($json);

        $mainUrl = $decoded->mainUrl;
        $url = $decoded->url;
        $last = $decoded->last;
        
        return new UrlMessage(
            $mainUrl,
            $url,
            $last
        );
    }
}
