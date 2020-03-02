<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class BoardPage extends Model implements BitcointalkQueries
{
    const TABLE = 'bitcointalk_board_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';
    
    public function getTableName(): string {
        return self::TABLE;
    }

    public function main_board() {
        $this->belongsTo(MainBoard::class);
    }

    public static function unsetLast(int $mainBoardId) {
        return self::query()
            ->where(self::COL_PARENT_URL, $mainBoardId)
            ->where(self::COL_LAST, true)
            ->update(array(self::COL_LAST => false));
    }

    public static function getByUrl(string $url): ?Model {
        return self::query()
            ->where("url", $url)
            ->get()
            ->first();
    }

    public static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }

    /**
     * @return BoardPage[]
     */
    public static function getAllUnParsed(): array {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get()
            ->all();
    }
    
    public static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    }
}
