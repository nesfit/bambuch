<?php
declare(strict_types=1);

namespace App\Models\Views;

use App\Models\Pg\Task;
use App\Models\Constants\TaskConstants;

class TaskView extends BaseView {
    public string $name;
    public string $frequency;
    public string $starting;
    public string $description;
    public array $frequencies;

    public function __construct(Task $taskData) {
//        $this->name = preg_split('/:/', $taskData->getAttribute(Task::COL_NAME))[1];
        $this->name = $taskData->getAttribute(Task::COL_NAME);
        $this->frequency = $taskData->getAttribute(Task::COL_FREQ) ?? TaskConstants::NONE;
        $this->starting = $taskData->getAttribute(Task::COL_STARTING) ?? '';
        $this->description = $taskData->getAttribute(Task::COL_DESC);
        $this->frequencies = [ TaskConstants::NONE, TaskConstants::MINUTE, TaskConstants::HOURLY, TaskConstants::DAILY, TaskConstants::WEEKLY ];
    }
    
    public function isSelected(string $option) {
        return $option === $this->frequency;
    } 
}