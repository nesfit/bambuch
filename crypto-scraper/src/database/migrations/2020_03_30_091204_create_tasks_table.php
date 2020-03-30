<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Pg\Task;

class CreateTasksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(Task::TABLE, function (Blueprint $table) {
            $table->id(Task::COL_ID);
            $table->string(Task::COL_NAME, 63);
            $table->string(Task::COL_FREQ, 63);
            $table->string(Task::COL_STARTING, 63);
            $table->string(Task::COL_DESC, 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(Task::TABLE);
    }
}
