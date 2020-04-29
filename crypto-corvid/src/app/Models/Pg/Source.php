<?php
declare(strict_types=1);

namespace App\Models\Pg;

use App\Console\Constants\Bitcoinabuse\BitcoinabuseCommands;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    const COL_ID            = 'id';
    const COL_NAME          = 'name';
    const COL_URL           = 'url';

    const TABLE             = 'sources';

    protected $table        = self::TABLE;
    protected $connection   = 'pgsql';

    public $timestamps = false;

    const SOURCES = [
        [self::COL_NAME => 'bitcointalk', self::COL_URL => BitcointalkCommands::BITCOINTALK_URL],
        [self::COL_NAME => 'bitcoinabuse', self::COL_URL => BitcoinabuseCommands::BITCOINABUSE_URL],
    ];
        
    public static function getByName(string $name): ?Source {
        return self::query()
            ->where(self::COL_NAME, $name)
            ->get()
            ->first();
    }
    
    public static function getAddresses(string $url) {
        return Address::query()
            ->leftJoin(
                Identity::TABLE, 
                Address::TABLE .'.'. Address::COL_ID,
                '=',
                Identity::TABLE .'.'. Identity::COL_ADDRID)
            ->where(Identity::TABLE .'.'. Identity::COL_URL, 'like', $url . '%')
            ->limit(100)
            ->get(Address::TABLE .'.*')
            ->all();
    }
}
