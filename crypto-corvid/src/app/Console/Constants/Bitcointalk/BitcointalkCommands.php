<?php
declare(strict_types=1);

namespace App\Console\Constants\Bitcointalk;

abstract class BitcointalkCommands {
    const BITCOINTALK = 'bct:';
    const BITCOINTALK_URL = 'https://bitcointalk.org';
    
    const START = self::BITCOINTALK . 'start';
    const STOP = self::BITCOINTALK . 'stop';
    
    /**
     * COMMAND SIGNATURES
     */
    const MAIN_BOARDS_PRODUCER = self::BITCOINTALK . 'main_boards_producer';
    const BOARD_PAGES_PRODUCER = self::BITCOINTALK . 'board_pages_producer';
    const ALL_MAIN_TOPICS_PRODUCER = self::BITCOINTALK . 'all_main_topics_producer';
    const MAIN_TOPICS_PRODUCER = self::BITCOINTALK . 'main_topics_producer';
    const TOPIC_PAGES_PRODUCER = self::BITCOINTALK . 'topic_pages_producer';
    const TOPIC_PAGES_CONSUMER = self::BITCOINTALK . 'topic_pages_consumer';
    const USER_PROFILES_PRODUCER = self::BITCOINTALK . 'user_profiles_producer';
    const USER_PROFILES_CONSUMER = self::BITCOINTALK . 'user_profiles_consumer';
    
    const UN_TOPIC_PAGES_PRODUCER = self::BITCOINTALK . 'un_topic_pages_producer';
    const UN_BOARD_PAGES_PRODUCER = self::BITCOINTALK . 'un_board_pages_producer';
    const UN_USER_PROFILES_PRODUCER = self::BITCOINTALK . 'un_user_profiles_producer';

    /**
     * COMMAND DESCRIPTIONS
     */
    const MAIN_BOARDS_PRODUCER_DESC = 'Scrapes main and child boards starting from index.';
    const BOARD_PAGES_PRODUCER_DESC = 'Scrapes main boards and extract board pages.';
    const ALL_MAIN_TOPICS_PRODUCER_DESC = 'Loads all main topics into Kafka.';
    const MAIN_TOPICS_PRODUCER_DESC = 'Loads main topics from board page.';
    const TOPIC_PAGES_PRODUCER_DESC = 'Loads topic pages from main topics.';
    const TOPIC_PAGES_CONSUMER_DESC = 'Scrapes topic pages and extracts metadata.';
    const USER_PROFILES_PRODUCER_DESC = 'Scrapes topic pages and extracts users.';
    const USER_PROFILES_CONSUMER_DESC = 'Scrapes user profiles and extracts addresses.';
    
    const UN_TOPIC_PAGES_PRODUCER_DESC = 'Loads unparsed topics pages into Kafka.';
    const UN_BOARD_PAGES_PRODUCER_DESC = 'Loads unparsed board pages into Kafka.';
    const UN_USER_PROFILES_PRODUCER_DESC = 'Loads unparsed user profiles into Kafka.';
}