<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;


trait UrlCalculations {
    /**
     * @param string $entity "topic|board"
     * @param int $entityId
     * @param int $from
     * @param int $to
     * @param int $offset
     * @return array
     */
    public static function calculateEntityPages(string $entity, int $entityId, int $from, int $to, int $offset): array {
        $entityPages = [];
        for (; $from <= $to; $from += $offset) {
            array_push($entityPages, sprintf('https://bitcointalk.org/index.php?%s=%s.%s', $entity, $entityId, $from));
        }
        return $entityPages;
    }
}