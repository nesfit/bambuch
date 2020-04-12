<?php
declare(strict_types=1);

namespace App\Console\Constants;

abstract class BitcointalkKafka {
    public const MAIN_BOARDS_TOPIC = "btalkMainBoards";
    public const MAIN_BOARDS_LOAD_GROUP = "btalkMainBoardsGroupLoad";
    public const BOARD_PAGES_TOPIC = "btalkBoardPages";
    public const BOARD_PAGES_LOAD_GROUP = "btalkBoardPagesGroupLoad";
    public const MAIN_TOPICS_TOPIC = "btalkMainTopics";
    public const MAIN_TOPICS_LOAD_GROUP = "btalkMainTopicsGroupLoad";
    public const TOPIC_PAGES_TOPIC = "btalkTopicUrl";
    public const TOPIC_PAGES_ADDR_GROUP = "btalkTopicUrlGroupAddress";
    public const TOPIC_PAGES_PROFILE_GROUP = "btalkTopicUrlGroupProfile";
    public const USER_PROFILES_TOPIC = "btalkUserProfiles";
    public const USER_PROFILES_LOAD_GROUP = "btalkUserProfilesGroup";
    
}