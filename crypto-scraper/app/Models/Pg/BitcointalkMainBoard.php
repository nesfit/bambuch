<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class BitcointalkMainBoard extends Model
{
    const COL_ID        = 'id';
    const COL_URL       = 'url'; 

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_main_boards';
    protected $table    = self::TABLE;
    protected $connection = 'pgsql';
    
    public function bitcointalk_board_pages() {
        return $this->hasMany(BitcointalkBoardPage::class);
    }

    public static function getByUrl(string $url) {
        return self::where("url", $url)->get()->first();
    }
    
    public static function mainBoardExists(string $url) {
        return self::getByUrl($url) !== null;
    }

}
