<?php 
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class MainTopic extends Model implements BitcointalkQueries
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

    public static function unsetLast(int $int) {
        return;
    }
    
    public static function getByUrl(string $url): ?Model {
        return self::query()
            ->where(self::COL_URL, $url)
            ->get()
            ->first();
    }

    public static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }

    /**
     * @return MainTopic[]
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
