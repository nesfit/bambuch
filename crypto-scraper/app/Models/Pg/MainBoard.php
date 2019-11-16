<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class MainBoard extends Model
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

    public static function getByUrl(string $url): ?MainBoard {
        return self::query()
            ->where(self::COL_URL, $url)
            ->get()
            ->first();
    }
    
    public static function mainBoardExists(string $url) {
        return self::getByUrl($url) !== null;
    }
    
    public static function getUnParsedBoards() {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->pluck(self::COL_URL);
    }
    
    public static function setParsedToAll(bool $value) {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    } 
}
