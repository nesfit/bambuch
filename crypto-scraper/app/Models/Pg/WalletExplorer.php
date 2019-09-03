<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class WalletExplorer extends Model
{
    const COL_ID            = 'id';
    const COL_CREATEDAT     = 'created_at';
    const COL_UPDATEDAT     = 'updated_at';
    const COL_CATEGORY      = 'category';
    const COL_OWNER         = 'owner';

    const TABLE             = 'wallet_explorer';
    
    protected $table        = self::TABLE;
    protected $connection   = 'pgsql';

    public static function getByOwner($owner) {
        return self::where("owner", $owner)->get()->first();
    }

    public static function getByOwnerLike($owner) {
        $dbInput = '%' . strtolower($owner) . '%';
        return self::where("owner", 'like', $dbInput)->get()->first();
    }
}
