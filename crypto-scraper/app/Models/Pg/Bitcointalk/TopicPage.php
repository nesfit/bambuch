<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class TopicPage extends Model implements BitcointalkQueries
{
    const COL_MAIN_TOPIC = 'main_topic_id';
    const TABLE         = 'bitcointalk_topic_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    public function getTableName(): string {
        return self::TABLE;
    }
    
    public function main_topic() {
        $this->belongsTo(MainTopic::class);
    }

    public static function unsetLastTopic(int $mainTopicId) {
        return self::query()
            ->where(self::COL_MAIN_TOPIC, $mainTopicId)
            ->where(self::COL_LAST, true)
            ->update(array(self::COL_LAST => false));
    }
    
    public static function getByUrl(string $url): ?Model {
        return self::query()
            ->where("url", $url)
            ->get()
            ->first();
    }

    public static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }

    /**
     * @return TopicPage[]
     */
    public static function getAllUnParsed(): array {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get()
            ->all();
    }
    
    public static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    }
}
