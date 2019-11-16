<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class BoardPage extends Model implements BitcointalkQueries
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';
    const COL_LAST      = 'last';
    const COL_MAIN_BOARD = 'main_board_id';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_board_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';
    
    public function main_board() {
        $this->belongsTo(MainBoard::class);
    }

    public static function unsetLastBoard(int $mainBoardId) {
        return self::query()
            ->where(self::COL_MAIN_BOARD, $mainBoardId)
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
