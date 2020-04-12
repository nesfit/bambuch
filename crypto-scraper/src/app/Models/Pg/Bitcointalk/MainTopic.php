<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

class MainTopic extends BitcointalkModel
{
    const TABLE = 'bitcointalk_main_topics';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    public function getTableName(): string {
        return self::TABLE;
    }
    
    public function board_topics() {
        return $this->hasMany(TopicPage::class);
    }

    public static function unsetLast(string $string) {
        return;
    }
}
