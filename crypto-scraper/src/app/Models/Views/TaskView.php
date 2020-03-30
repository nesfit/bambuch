<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Task;

class TaskView extends BaseView {
    public string $name;
    public string $frequency;
    public string $starting;
    public string $description;
    public array $frequencies;
    
    const NONE = 'none';
    const MINUTE = 'every minute';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    
    public function __construct(Task $taskData) {
        $this->name = preg_split('/:/', $taskData->getAttribute(Task::COL_NAME))[1];
        $this->frequency = $taskData->getAttribute(Task::COL_FREQ) ?? self::NONE;
        $this->starting = $taskData->getAttribute(Task::COL_STARTING) ?? '';
        $this->description = $taskData->getAttribute(Task::COL_DESC);
        $this->frequencies = [ self::NONE, self::MINUTE, self::HOURLY, self::DAILY, self::WEEKLY ];
    }
    
    public function isSelected(string $option) {
        return $option === $this->frequency;
    } 
}