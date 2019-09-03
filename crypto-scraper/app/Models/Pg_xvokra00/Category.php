<?php
/**
 * Created by PhpStorm.
 * User: Mordeth
 * Date: 09.12.2018
 * Time: 11:29
 */

namespace App\Models\Pg_xvokra00;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string name
 * @property string color
 */
class Category extends Model
{
    const COL_ID            = 'id';
    const COL_NAME          = 'name';
    const COL_COLOR         = 'color';
    const COL_CREATEDAT     = 'created_at';
    const COL_UPDATEDAT     = 'updated_at';

    const TABLE             = 'categories';

    const CAT_1  = "Other";            const CAT_1_COLOR  = "#336600";
    const CAT_2  = "Person";           const CAT_2_COLOR  = "#339966";
    const CAT_3  = "Exchange";         const CAT_3_COLOR  = "#0066ff";
    const CAT_4  = "Hosted Wallet";    const CAT_4_COLOR  = "#00ccff";
    const CAT_5  = "Mixer";            const CAT_5_COLOR  = "#996633";
    const CAT_6  = "Mining Pool";      const CAT_6_COLOR  = "#cc9900";
    const CAT_7  = "Miner";            const CAT_7_COLOR  = "#ffff00";
    const CAT_8  = "Gambling";         const CAT_8_COLOR  = "#ff9999";
    const CAT_9  = "Merchant";         const CAT_9_COLOR  = "#ffcc99";
    const CAT_10 = "Darknet Market";   const CAT_10_COLOR  = "#ff0000";
    const CAT_11 = "Scam";             const CAT_11_COLOR = "#cc0066";
    const CAT_12 = "Theft";            const CAT_12_COLOR = "#990033";
    const CAT_13 = "Ransom";           const CAT_13_COLOR = "#ff3300";

    const CATEGORIES = [
            [Category::CAT_1,Category::CAT_1_COLOR],
            [Category::CAT_2,Category::CAT_2_COLOR],
            [Category::CAT_3,Category::CAT_3_COLOR],
            [Category::CAT_4,Category::CAT_4_COLOR],
            [Category::CAT_5,Category::CAT_5_COLOR],
            [Category::CAT_6,Category::CAT_6_COLOR],
            [Category::CAT_7,Category::CAT_7_COLOR],
            [Category::CAT_8,Category::CAT_8_COLOR],
            [Category::CAT_9,Category::CAT_9_COLOR],
            [Category::CAT_10,Category::CAT_10_COLOR],
            [Category::CAT_11,Category::CAT_11_COLOR],
            [Category::CAT_12,Category::CAT_12_COLOR],
            [Category::CAT_13,Category::CAT_13_COLOR],
    ];

    protected $table        = self::TABLE;
    protected $connection   = 'pgsql';

    public $timestamps = false;

    public function addresses()
    {
        return $this->belongsToMany(Address::class, Address::TABLE_CATEGORY);
    }

}

