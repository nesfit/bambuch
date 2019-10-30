<?php

namespace App\Models\Pg;

use Illuminate\Database\Eloquent\Model;

class BitcointalkTopicPage extends Model
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';
    const COL_LAST      = 'last';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_topic_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';
}
