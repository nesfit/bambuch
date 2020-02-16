<?php
declare(strict_types=1);

namespace App\Console\Constants;

class BitcointalkKafka {
    public const TOPIC_PAGES_TOPIC = "btalkTopicUrl";
    public const TOPIC_PAGES_GROUP = "btalkTopicUrlGroup";
    public const BOARD_PAGES_TOPIC = "btalkBoardPages";
    public const BOARD_PAGES_STORE_GROUP = "btalkBoardPagesGroupStore";
    public const BOARD_PAGES_LOAD_GROUP = "btalkBoardPagesGroupLoad";
}