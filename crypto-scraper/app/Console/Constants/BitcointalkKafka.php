<?php
declare(strict_types=1);

namespace App\Console\Constants;

abstract class BitcointalkKafka {
    public const MAIN_BOARDS_TOPIC = "btalkMainBoards";
    public const MAIN_BOARDS_STORE_GROUP = "btalkMainBoardsGroupStore";
    public const MAIN_BOARDS_LOAD_GROUP = "btalkMainBoardsGroupLoad";
    public const BOARD_PAGES_TOPIC = "btalkBoardPages";
    public const BOARD_PAGES_STORE_GROUP = "btalkBoardPagesGroupStore";
    public const BOARD_PAGES_LOAD_GROUP = "btalkBoardPagesGroupLoad";
    public const MAIN_TOPICS_TOPIC = "btalkMainTopics";
    public const MAIN_TOPICS_STORE_GROUP = "btalkMainTopicsGroupStore";
    public const MAIN_TOPICS_LOAD_GROUP = "btalkMainTopicsGroupLoad";
    public const TOPIC_PAGES_TOPIC = "btalkTopicUrl";
    public const TOPIC_PAGES_STORE_GROUP = "btalkTopicUrlGroupStore";
    public const TOPIC_PAGES_LOAD_GROUP = "btalkTopicUrlGroupLoad";
}