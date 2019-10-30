<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class BitcointalkBoardPage extends Model
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
    
    public function bitcointalk_main_boards() {
        $this->belongsTo(BitcointalkMainBoard::class);
    }
}
