<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class MainBoard extends Model
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_main_boards';
    protected $table    = self::TABLE;
    protected $connection = 'pgsql';
    
    public function board_pages() {
        return $this->hasMany(BoardPage::class);
    }

    public static function getByUrl(string $url): ?MainBoard {
        return self::where("url", $url)->get()->first();
    }
    
    public static function mainBoardExists(string $url) {
        return self::getByUrl($url) !== null;
    }
    
    public static function getAllBoards() {
        return self::all()->pluck(self::COL_URL);
    }
}
