<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

class MainBoard extends BitcointalkModel {
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
}
