<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

class TopicPage extends BitcointalkModel
{
    const TABLE = 'bitcointalk_topic_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    public function getTableName(): string {
        return self::TABLE;
    }
    
    public function main_topic() {
        $this->belongsTo(MainTopic::class);
    }
}
