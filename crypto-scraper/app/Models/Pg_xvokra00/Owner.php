<?php
/**
 * Created by PhpStorm.
 * User: Mordeth
 * Date: 09.12.2018
 * Time: 12:01
 */

namespace App\Models\Pg_xvokra00;


use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    const COL_ID          = 'id';
    const COL_NAME        = 'name';
    const COL_PLACEHOLDER = 'placeholder';

    const TABLE           = 'owners';
    protected $table      = self::TABLE;
    protected $connection = 'pgsql';
    public $fillable = [self::COL_NAME];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

}