<?php

namespace App\Models\Pg;

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
        [self::COL_NAME => 'bitcointalk', self::COL_URL => 'https://bitcointalk.org'],
        [self::COL_NAME => 'bitcoinabuse', self::COL_URL => 'https://www.bitcoinabuse.com'],
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
