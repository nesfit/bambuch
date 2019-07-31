<?php
/**
 * Created by PhpStorm.
 * User: Mordeth
 * Date: 09.12.2018
 * Time: 11:48
 */

namespace App\Models\Pg;


use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    const COL_ID        = 'id';
    const COL_SOURCE    = 'source';
    const COL_LABEL     = 'label';
    const COL_URL       = 'url';
    const COL_DESC      = 'description';
    const COL_ADDRID    = 'address_id';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'identies';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';
    public $fillable = [self::COL_SOURCE, self::COL_LABEL, self::COL_ADDRID, self::COL_URL, self::COL_DESC];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

}