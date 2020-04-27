<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

abstract class BitcointalkModel extends Model {
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';
    const COL_LAST      = 'last';
    const COL_PARENT_URL = 'parent_url';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    /**
     * @return BitcointalkModel[]
     */
    public static function getAllUnParsed() {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get()
            ->all();
    }

    public static function getFirstUnparsed(): ?BitcointalkModel {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get()
            ->first();
    }

    public static function getAll(): array {
        return self::query()
            ->get(self::COL_URL)
            ->toArray();
    }
    
    public static function getLast(string $parentUrl): ?BitcointalkModel {
        return self::query()
            ->where(self::COL_PARENT_URL, $parentUrl)
            ->where(self::COL_LAST, true)
            ->get()
            ->first();
    }

    public static function getByUrl(string $url): ?BitcointalkModel {
        return self::query()
            ->where(self::COL_URL, $url)
            ->get()
            ->first();
    }

    public static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    }

    public static function setParsedByUrl(string $url): bool {
        return boolval(self::query()
            ->where(self::COL_URL, $url)
            ->update(array(self::COL_PARSED => true)));
    }

    public static function unsetLast(string $parentUrl) {
        return self::query()
            ->where(self::COL_PARENT_URL, $parentUrl)
            ->where(self::COL_LAST, true)
            ->update(array(self::COL_LAST => false));
    }

    public static function unparseLast(string $parentUrl) {
        return self::query()
            ->where(self::COL_PARENT_URL, $parentUrl)
            ->where(self::COL_LAST, true)
            ->update(array(self::COL_PARSED => false));
    }

    public static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }
    
    abstract public function getTableName(): string;
}