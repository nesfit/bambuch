<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class MainBoard extends Model implements BitcointalkQueries
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_main_boards';
    protected $table    = self::TABLE;
    protected $connection = 'pgsql';
    
    public function board_pages() {
        return $this->hasMany(BoardPage::class);
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

    /**
     * @return MainBoard[]
     */
    public static function getAllUnParsed(): array {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get(self::COL_URL)
            ->all();
    }
    
    public static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    } 
}
