<?php

namespace App\Models\Pg\Bitcointalk;

use Illuminate\Database\Eloquent\Model;

class TopicPage extends Model
{
    const COL_ID        = 'id';
    const COL_URL       = 'url';
    const COL_PARSED    = 'parsed';
    const COL_LAST      = 'last';
    const COL_MAIN_TOPIC = 'main_topic_id';

    const COL_CREATEDAT = 'created_at';
    const COL_UPDATEDAT = 'updated_at';

    const TABLE         = 'bitcointalk_topic_pages';
    protected $table = self::TABLE;
    protected $connection = 'pgsql';

    public function main_topic() {
        $this->belongsTo(MainTopic::class);
    }

    public static function getByUrl(string $url) {
        return self::where("url", $url)->get()->first();
    }

    public static function topicPageExists(string $url) {
        return self::getByUrl($url) !== null;
    }

    public static function unsetLastTopic(int $mainTopicId) {
        return self::where(self::COL_MAIN_TOPIC, $mainTopicId)
            ->where(self::COL_LAST, true)
            ->update(array(self::COL_LAST => false));
    }
}
