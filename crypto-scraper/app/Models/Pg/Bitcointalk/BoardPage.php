<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

class BoardPage extends BitcointalkModel {
    const TABLE = 'bitcointalk_board_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';
    
    public function getTableName(): string {
        return self::TABLE;
    }

    public function main_board() {
        $this->belongsTo(MainBoard::class);
    }
}
