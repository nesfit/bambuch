<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;


use Illuminate\Database\Eloquent\Model;

interface BitcointalkQueries {
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';
    const COL_LAST      = 'last';
    
    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';
    
    static function getAllUnParsed(): array;
    static function setParsedToAll(bool $value): void;
    static function getByUrl(string $url): ?Model;
    static function exists(string $url): bool;
    
    public function getTableName(): string;
}
