<?php
declare(strict_types=1);

namespace App\Models;

use App\Console\Utils;

class ParsedAddress {
    private $owner, $url, $label, $source, $address, $type, $explicitCategory;
    
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
    
    public function getData(): array {
        return [$this->owner, $this->url, $this->label, $this->source, $this->address, $this->type, $this->explicitCategory];
    }
    
    public function createTSVData(): string {
        $cleanArray = array_reduce(self::getData(),
            function ($acc, $value)  {
                array_push($acc, Utils::cleanText($value));
                return $acc;
            }, []
        );
        return implode("\t", $cleanArray);
    }
    
    public static function ownerExists(string $owner, ParsedAddress ...$addresses) {
        return array_reduce($addresses, function ($acc, ParsedAddress $data) use ($owner) {
            return $acc || $data->getOwner() === $owner;
        }, false);
    }    
}