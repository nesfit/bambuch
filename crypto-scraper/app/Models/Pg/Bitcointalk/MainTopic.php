<?php

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class MainTopic extends Model
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_main_topics';
    protected $table    = self::TABLE;
    protected $connection = 'pgsql';

    public function board_topics() {
        return $this->hasMany(TopicPage::class);
    }

    public static function getByUrl(string $url): ?MainTopic {
        return self::where(self::COL_URL, $url)->get()->first();
    }

    public static function mainTopicExists(string $url) {
        return self::getByUrl($url) !== null;
    }

    public static function getNonParsedTopics() {
        return self::where(self::COL_PARSED, false)->pluck(self::COL_URL);
    }
}
