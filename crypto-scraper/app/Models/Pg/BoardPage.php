<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class BoardPage extends Model
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

    public static function getByUrl(string $url) {
        return self::where("url", $url)->get()->first();
    }

    public static function boardPageExists(string $url) {
        return self::getByUrl($url) !== null;
    }
}
