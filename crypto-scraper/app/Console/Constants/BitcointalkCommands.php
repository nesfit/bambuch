<?php
declare(strict_types=1);

namespace App\Console\Constants;

abstract class BitcointalkCommands {
    const BITCOINTALK = 'bct:';
    const BITCOINTALK_URL = 'https://bitcointalk.org';
    const INITIALIZE_BOARDS = self::BITCOINTALK . 'initialize_boards';
    const RUN_BOARDS = self::BITCOINTALK . 'run_boards';
    const RUN_MAIN_TOPICS = self::BITCOINTALK . 'run_main_topics';
    const RUN_TOPICS_PAGE = self::BITCOINTALK . 'run_topic_page';
    const RUN_TOPICS_PAGES = self::BITCOINTALK . 'run_topic_pages';
    const RUN_USER_PROFILES = self::BITCOINTALK . 'run_user_profiles';
    const LOAD_BOARDS = self::BITCOINTALK . 'load_boards';
    const LOAD_MAIN_TOPICS = self::BITCOINTALK . 'load_main_topics';
    const LOAD_TOPICS_PAGES = self::BITCOINTALK . 'load_topic_pages';
    const LOAD_USER_PROFILES = self::BITCOINTALK . 'load_user_profiles';
    const PARSE_USER_PROFILE = self::BITCOINTALK . 'parse_user_profile';
    const PARSE_TOPIC_MESSAGES = self::BITCOINTALK . 'parse_topic_messages';

    const MAIN_BOARDS_PRODUCER = self::BITCOINTALK . 'main_boards_producer';
    const BOARD_PAGES_PRODUCER = self::BITCOINTALK . 'board_pages_producer';
    const MAIN_TOPICS_PRODUCER = self::BITCOINTALK . 'main_topics_producer';
    const TOPIC_PAGES_PRODUCER = self::BITCOINTALK . 'topic_pages_producer';
    const TOPIC_PAGES_CONSUMER = self::BITCOINTALK . 'topic_pages_consumer';
    const USER_PROFILES_PRODUCER = self::BITCOINTALK . 'user_profiles_producer';
    const USER_PROFILES_CONSUMER = self::BITCOINTALK . 'user_profiles_consumer';

    const START = self::BITCOINTALK . 'start';
    const STOP = self::BITCOINTALK . 'stop';
}