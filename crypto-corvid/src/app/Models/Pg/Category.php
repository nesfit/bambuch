<?php
declare(strict_types=1);

namespace App\Models\Pg;

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
    const CAT_10 = "Darknet Market";   const CAT_10_COLOR = "#ff0000";
    const CAT_11 = "Scam";             const CAT_11_COLOR = "#cc0066";
    const CAT_12 = "Theft";            const CAT_12_COLOR = "#990033";
    const CAT_13 = "Ransom";           const CAT_13_COLOR = "#ff3300";
    const CAT_14 = "Sextortion";           const CAT_14_COLOR = "#00FFFB";

    const CATEGORIES = [
            ["name" => Category::CAT_1, "color" => Category::CAT_1_COLOR],
            ["name" => Category::CAT_2, "color" => Category::CAT_2_COLOR],
            ["name" => Category::CAT_3, "color" => Category::CAT_3_COLOR],
            ["name" => Category::CAT_4, "color" => Category::CAT_4_COLOR],
            ["name" => Category::CAT_5, "color" => Category::CAT_5_COLOR],
            ["name" => Category::CAT_6, "color" => Category::CAT_6_COLOR],
            ["name" => Category::CAT_7, "color" => Category::CAT_7_COLOR],
            ["name" => Category::CAT_8, "color" => Category::CAT_8_COLOR],
            ["name" => Category::CAT_9, "color" => Category::CAT_9_COLOR],
            ["name" => Category::CAT_10, "color" => Category::CAT_10_COLOR],
            ["name" => Category::CAT_11, "color" => Category::CAT_11_COLOR],
            ["name" => Category::CAT_12, "color" => Category::CAT_12_COLOR],
            ["name" => Category::CAT_13, "color" => Category::CAT_13_COLOR],
            ["name" => Category::CAT_14, "color" => Category::CAT_14_COLOR],
    ];

    protected $table        = self::TABLE;
    protected $connection   = 'pgsql';

    public $timestamps = false;

    public static function getByName(string $name) {
        return self::query()
            ->where("name", $name)
            ->get()
            ->first();
    }
    
    public static function getByPartialMatch(string $text) {
        $name = self::getNameFromMatch($text);
        return self::getByName($name);
    }

    private static function getNameFromMatch(string $text) {
        switch (true) {
            case preg_match('/' . self::CAT_2 . '/i', $text): return self::CAT_2;
            case preg_match('/' . self::CAT_3 . '/i', $text): return self::CAT_3;
            case preg_match('/' . self::CAT_4 . '|wallet/i', $text): return self::CAT_4;
            case preg_match('/' . self::CAT_5 . '/i', $text): return self::CAT_5;
            case preg_match('/' . self::CAT_6 . '|pool/i', $text): return self::CAT_6;
            case preg_match('/' . self::CAT_7 . '/i', $text): return self::CAT_7;
            case preg_match('/' . self::CAT_8 . '/i', $text): return self::CAT_8;
            case preg_match('/' . self::CAT_9 . '/i', $text): return self::CAT_9;
            case preg_match('/' . self::CAT_10 . '|darkweb|darknet/i', $text): return self::CAT_10;
            case preg_match('/' . self::CAT_11 . '/i', $text): return self::CAT_11;
            case preg_match('/' . self::CAT_12 . '/i', $text): return self::CAT_12;
            case preg_match('/' . self::CAT_13 . '/i', $text): return self::CAT_13;
            case preg_match('/' . self::CAT_14 . '/i', $text): return self::CAT_14;
        }
        return self::CAT_1;
    }

    public function addresses()
    {
        return $this->belongsToMany(Address::class, Address::TABLE_CATEGORY);
    }
}
