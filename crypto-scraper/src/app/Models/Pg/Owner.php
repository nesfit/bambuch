<?php
/**
 * Project: BitInfoCharts parser
 * Author: Vladislav Bambuch - xbambu03@stud.fit.vutbr.cz
 */

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    const COL_ID          = 'id';
    const COL_NAME        = 'name';
    const COL_PLACEHOLDER = 'placeholder';
    const COL_CREATEDAT   = 'created_at';
    const COL_UPDATEDAT   = 'updated_at';

    const TABLE           = 'owners';
    protected $table      = self::TABLE;
    protected $connection = 'pgsql';

    public static function getByName($name) {
        return self::query()
            ->where("name", $name)
            ->get()
            ->first();
    }

    public static function getAllNames() {
        return self::query()->get()->reduce(function ($acc, $owner) {
            $acc[$owner->name] = [];
            return $acc;
        }, []);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}