<?php
declare(strict_types=1);

namespace App\Console\Constants;

abstract class CommonKafka {
    const SCRAPE_RESULTS_TOPIC = "scrapeResults";   
    const SCRAPE_RESULTS_GROUP = "scrapeResultsGroup";
}