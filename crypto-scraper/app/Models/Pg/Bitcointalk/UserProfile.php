<?php
declare(strict_types=1);

namespace App\Models\Pg\Bitcointalk;

class UserProfile extends BitcointalkModel
{
    const TABLE         = 'bitcointalk_user_profiles';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    public function getTableName(): string {
        return self::TABLE;
    }

    public static function unsetLast(int $int) {
        return;
    }
}
