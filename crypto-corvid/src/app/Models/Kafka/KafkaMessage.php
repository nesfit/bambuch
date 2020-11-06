<?php
declare(strict_types=1);

namespace App\Models\Kafka;

interface KafkaMessage {
    public function toJSON(): string;
    public static function fromJSON(string $json);
}