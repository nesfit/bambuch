<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string address
 * @property string source
 * @property string label
 * @property string url
 * @property int report_id
 */
class Identity extends Model
{
    const COL_ID        = 'id';
    const COL_ADDRESS   = 'address';
    const COL_SOURCE    = 'source';
    const COL_LABEL     = 'label';
    const COL_URL       = 'url';
    const COL_REPORT    = 'report_id';
    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'identities';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::COL_ADDRESS, self::COL_SOURCE, self::COL_LABEL, self::COL_URL, self::COL_REPORT
    ];

    protected $casts = [
        self::COL_ADDRESS => 'string',
        self::COL_SOURCE => 'string',
        self::COL_LABEL => 'string',
        self::COL_URL => 'string',
        self::COL_REPORT => 'integer',
    ];

}
