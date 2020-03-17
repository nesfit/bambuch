<?php
declare(strict_types=1);

namespace App\Console\Base\Bitcointalk;


trait UrlCalculations {
    /**
     * @param string $entity "topic|board"
     * @param int $entityId
     * @param int $from
     * @param int $to
     * @return array
     */
    public static function calculateEntityPages(string $entity, int $entityId, int $from, int $to): array {
        $entityPages = [];
        for (; $from <= $to; $from += 20) {
            array_push($entityPages, sprintf('https://bitcointalk.org/index.php?%s=%s.%s', $entity, $entityId, $from));
        }
        return $entityPages;
    }
}