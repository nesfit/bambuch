<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class MainBoard extends Model implements BitcointalkQueries
{
    const TABLE         = 'bitcointalk_main_boards';
    protected $table    = self::TABLE;
    protected $connection = 'pgsql';

    public function getTableName(): string {
        return self::TABLE;
    }
    
    public function board_pages() {
        return $this->hasMany(BoardPage::class);
    }

    public static function unsetLast(int $int) {
        return;
    }

    public static function getByUrl(string $url): ?Model {
        return self::query()
            ->where(self::COL_URL, $url)
            ->get()
            ->first();
    }
    
    public static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }

    public static function getAllUnParsed(): array {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get(self::COL_URL)
            ->toArray();
    }
    
    public static function getFirstUnparsed(): MainBoard {
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
    
    public static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    } 
}
