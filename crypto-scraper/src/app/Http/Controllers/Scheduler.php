<?php
//declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pg\Task;
use App\Models\Views\TaskView;
use Barryvdh\Debugbar\Facade;
use Illuminate\Support\Facades\Request;

class Scheduler extends Controller {
    
    public function get() {
        $tasks = Task::all()->all();
        $taskData = array_map(function ($item) { return new TaskView($item); }, $tasks);

        return view('scheduler', [
            'tasks' => $taskData
        ]);
    }
    
    public function make() {
        ['_token' => $token, 'frequency' => $frequency, 'starting' => $starting, 'name' => $name] = Request::all();
        
        Facade::debug($frequency);
        Facade::debug($starting);
        Facade::debug($name);

        
        return redirect('/scheduler');
    }
}
