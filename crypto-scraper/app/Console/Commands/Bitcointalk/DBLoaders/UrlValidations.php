<?php
declare(strict_types=1);

namespace App\Console\Commands\Bitcointalk\DBLoaders;


trait UrlValidations {
    /**
     * @param string $entity "topic|board"
     * @param array $allItems
     * @return array
     */
    public static function getMainEntity(string $entity, array $allItems): array {
        return array_filter($allItems, function (string $item) use ($entity) {
            return preg_match('/'. $entity .'=\d+\.0$/', $item, $matches) === 1;
        });
    }

    /**
     * @param string $entity "topic|board"
     * @param string $url
     * @return bool
     */
    public static function mainEntityValid(string $entity, string $url): bool {
        return preg_match('/'. $entity .'=\d+\.0$|^https:\/\/bitcointalk.org$/', $url, $matches) === 1;
    }

    /**
     * @param string $entity "topic|board"
     * @param string $url
     * @return bool
     */
    public static function pageEntityValid(string $entity, string $url): bool {
        return preg_match('/'. $entity .'=\d+\.\d+$/', $url, $matches) === 1;
    }

    /**
     * @param string $entity "topic|board"
     * @param array $allBoards
     * @return array
     */
    public static function getEntityPages(string $entity, array $allBoards): array {
        return array_filter($allBoards, function (string $item) use ($entity) {
            return preg_match('/'. $entity .'=\d+\.[^0]\d+$/', $item, $matches) === 1;
        });
    }

    /**
     * @param string $entity "topic|board"
     * @param string $url
     * @return int|null
     */
    public static function getEntityPageId(string $entity, string $url): ?int {
        preg_match('/'. $entity .'=\d+\.(\d+)$/', $url, $matches);
        return isset($matches[1]) ? intval($matches[1]) : null;
    }

    /**
     * @param string $entity "topic|board"
     * @param string $url
     * @return int|null
     */
    public static function getMainEntityId(string $entity, string $url): ?int {
        preg_match('/'. $entity .'=(\d+)\.\d+$/', $url, $matches);
        return isset($matches[1]) ? intval($matches[1]) : null;
    }
}