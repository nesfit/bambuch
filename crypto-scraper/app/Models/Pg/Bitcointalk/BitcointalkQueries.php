<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;


use Illuminate\Database\Eloquent\Model;

interface BitcointalkQueries {
    static function getAllUnParsed(): array;
    static function setParsedToAll(bool $value): void;
    static function getByUrl(string $url): ?Model;
    static function exists(string $url): bool;
}
