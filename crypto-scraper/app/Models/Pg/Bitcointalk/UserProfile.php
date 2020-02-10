<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model implements BitcointalkQueries
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_user_profiles';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    /**
     * @return UserProfile[]
     */
    static function getAllUnParsed(): array {
        return self::query()
            ->where(self::COL_PARSED, false)
            ->get()
            ->all();
    }

    static function setParsedToAll(bool $value): void {
        self::query()
            ->whereNotNull(self::COL_ID)
            ->update(array(self::COL_PARSED => $value));
    }

    static function getByUrl(string $url): ?Model {
        return self::query()
            ->where("url", $url)
            ->get()
            ->first();
    }

    static function exists(string $url): bool {
        return self::getByUrl($url) !== null;
    }
}
