<?php
/**
 * Created by PhpStorm.
 * User: Mordeth
 * Date: 09.12.2018
 * Time: 11:44
 */

namespace App\Models\Pg;


use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    const COL_ID            = 'id';
    const COL_ADDRESS       = 'address';
    const COL_CRYPTO        = 'crypto';
    const COL_OWNER         = 'owner_id';
    const COL_COLOR         = 'color';
    const COL_CREATEDAT     = 'created_at';
    const COL_UPDATEDAT     = 'updated_at';

    const COL_ADDRID        = 'address_id';
    const COL_CATID         = 'category_id';

    const TABLE             = 'addresses';
    const TABLE_CATEGORY    = 'address_has_categories';

    protected $table        = self::TABLE;
    protected $connection   = 'pgsql';
    public $fillable = [self::COL_ADDRESS, self::COL_CRYPTO, self::COL_OWNER];

    public function categories()
    {
        return $this->belongsToMany(Category::class, Address::TABLE_CATEGORY);
    }

    public function identities() {
        return $this->hasMany(Identity::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }


}