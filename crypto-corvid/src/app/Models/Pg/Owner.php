<?php
declare(strict_types=1);

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

    public static function getByName($name): ?Owner {
        return self::query()
            ->where("name", $name)
            ->get()
            ->first();
    }
    
    public static function getByNameWithCreate($name): Owner {
        $existing = self::getByName($name);
        if ($existing === null) {
            $owner = new Owner();
            $owner->name = $name;
            $owner->save();
            return $owner;
        }
        return $existing;
    }

    public static function getAllNames() {
        return self::query()->get()->reduce(function ($acc, $owner) {
            $acc[$owner->name] = [];
            return $acc;
        }, []);
    }

    /**
     * @return Owner[]
     */
    public static function getAll() {
        return self::query()->get()->all();
    }
    
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}