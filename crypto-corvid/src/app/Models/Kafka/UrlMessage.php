<?php
declare(strict_types=1);

namespace App\Models\Kafka;

use Serializable;

class UrlMessage implements Serializable {
    public string $mainUrl;
    public string $url;
    public bool $last;
    
    public function __construct(string $mainUrl, string $url, bool $last) {
        $this->mainUrl = $mainUrl;
        $this->url = $url;
        $this->last = $last;
    }
    
    public function serialize(): string {
        return json_encode([
            'mainUrl' => $this->mainUrl, 
            'url' => $this->url, 
            'last' => $this->last
        ]);
    }
    
    public function unserialize($serialized) {
        $json = json_decode($serialized);

        $this->mainUrl = $json->mainUrl;
        $this->url = $json->url;
        $this->last = $json->last;
    }
    
    public function encodeData() {
        return serialize($this);
    } 
    
    public static function decodeData($encoded): UrlMessage {
        return unserialize($encoded);
    }
}
