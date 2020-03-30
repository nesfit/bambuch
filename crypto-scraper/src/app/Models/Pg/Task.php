<?php
declare(strict_types=1);

namespace App\Models\Pg;

use App\Console\Constants\BitcointalkCommands;
use App\Models\Constants\TaskConstants;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_FREQ = 'frequency';
    const COL_STARTING = 'starting'; 
    const COL_DESC = 'description';

    const TABLE = 'tasks';
    
    protected $table        = self::TABLE;
    protected $connection = 'pgsql';

    public $timestamps = false;
    
    const TASKS = [
        [ 
            self::COL_NAME => BitcointalkCommands::MAIN_BOARDS_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::MAIN_BOARDS_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::BOARD_PAGES_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::BOARD_PAGES_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::MAIN_TOPICS_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::MAIN_TOPICS_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::TOPIC_PAGES_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::TOPIC_PAGES_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::TOPIC_PAGES_CONSUMER, 
            self::COL_DESC => BitcointalkCommands::TOPIC_PAGES_CONSUMER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::USER_PROFILES_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::USER_PROFILES_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcointalkCommands::USER_PROFILES_CONSUMER, 
            self::COL_DESC => BitcointalkCommands::USER_PROFILES_CONSUMER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
    ];
    
    public static function getByName(string $name): ?Task {
        return self::query()
            ->where(self::COL_NAME, $name)
            ->get()
            ->first();
    }

    /**
     * @return Task[]
     */
    public static function getOrdered() {
        return self::query()
            ->orderBy(self::COL_ID, 'asc')
            ->get()
            ->all();
    }
}
