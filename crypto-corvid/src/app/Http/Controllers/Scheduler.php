<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Constants\TaskConstants;
use App\Models\Pg\Task;
use App\Models\Views\TaskView;
use Illuminate\Support\Facades\Request;

class Scheduler extends Controller {
    
    public function get() {
        $tasks = Task::getOrdered();
        $taskData = array_map(function ($item) { return new TaskView($item); }, $tasks);

        return view('scheduler', [
            'tasks' => $taskData
        ]);
    }
    
    public function make() {
        ['_token' => $token, 'frequency' => $frequency, 'starting' => $starting, 'name' => $name] = Request::post();
        
        $task = Task::getByName($name);
        if ($task && $starting) {
            if ($frequency === TaskConstants::NONE) {
                $task->setAttribute(Task::COL_STARTING, "00:00");
            } else {
                $task->setAttribute(Task::COL_STARTING, $starting);
            }
            $task->setAttribute(Task::COL_FREQ, $frequency);
            $task->save();
        }

        return redirect('/scheduler');
    }
}
