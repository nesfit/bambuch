<?php
declare(strict_types=1);

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    const COL_ID        = 'id';
    const COL_SOURCE    = 'source';
    const COL_LABEL     = 'label';
    const COL_URL       = 'url';
    const COL_ADDRID    = 'address_id';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'identies';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    /**
     * @param $addr_id
     * @return Identity[]
     */
    public static function getByAddress($addr_id) {
        return self::query()
            ->where("address_id", $addr_id)
            ->get()
            ->all();
    }

    public static function getByUrl(string $url) {
        return self::query()
            ->where(self::COL_URL, 'like', $url)
            ->get()
            ->all();
    }
    
    public static function getIdentity($source, $url, $label, $addr_id) {
        return self::query()
            ->where("source", $source)
            ->where("url", $url)
            ->where("label", $label)
            ->where("address_id", $addr_id)
            ->first();
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

}