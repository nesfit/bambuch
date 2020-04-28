<?php
declare(strict_types=1);

namespace App\Models\Pg;

use App\Console\Constants\Bitcoinabuse\BitcoinabuseCommands;
use App\Console\Constants\Bitcointalk\BitcointalkCommands;
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
            self::COL_NAME => BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER, 
            self::COL_DESC => BitcointalkCommands::ALL_MAIN_TOPICS_PRODUCER_DESC,
            self::COL_FREQ => TaskConstants::NONE,
            self::COL_STARTING => TaskConstants::MIDNIGHT,
        ],
        [ 
            self::COL_NAME => BitcoinabuseCommands::LOAD_CSV_DATA, 
            self::COL_DESC => BitcoinabuseCommands::LOAD_CSV_DATA_DESC,
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

    /**
     * @return Task[]
     */
    public static function getEnabled() {
        return self::query()
            ->where(self::COL_FREQ, '!=', TaskConstants::NONE)
            ->get()
            ->all();
    }
}
