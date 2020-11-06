<?php
declare(strict_types=1);

namespace App\Models\Kafka;

use App\Console\Base\Common\Utils;

class ParsedAddress implements KafkaMessage {
    public string $owner, $url, $label, $source, $address, $type, $explicitCategory;
    
    public function __construct(string $owner, string $url, string $label, string $source, string $address, int $type, string $explicitCategory) {
        $this->owner = $owner;
        $this->url = $url;
        $this->label = $label;
        $this->source = $source;
        $this->address = $address;
        $this->type = $type;
        $this->explicitCategory = $explicitCategory;        
    }
    
    public function getOwner(): string {
        return $this->owner;
    }
    
    public static function ownerExists(string $owner, ParsedAddress ...$addresses) {
        return array_reduce($addresses, function ($acc, ParsedAddress $data) use ($owner) {
            return $acc || $data->getOwner() === $owner;
        }, false);
    }

    public function toJSON(): string {
        return json_encode([
            'owner' => $this->owner,
            'url' => $this->url,
            'label' => $this->label,
            'source' => $this->source,
            'address' => $this->address,
            'type' => $this->type,
            'explicitCategory' => $this->explicitCategory
        ]);
    }

    public static function fromJSON(string $json): ParsedAddress {
        $decoded = json_decode($json);

        $owner = Utils::cleanText($decoded->owner);
        $url = Utils::cleanText($decoded->url);
        $label = Utils::cleanText($decoded->label);
        $source = Utils::cleanText($decoded->source);
        $address = Utils::cleanText($decoded->address);
        $type = Utils::cleanText($decoded->type);
        $explicitCategory = Utils::cleanText($decoded->explicitCategory);

        return new ParsedAddress(
            $owner,
            $url,
            $label,
            $source,
            $address,
            $type,
            $explicitCategory
        );
    }
}