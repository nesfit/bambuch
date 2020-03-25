<?php
use App\Models\Pg\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create(Category::TABLE, function (Blueprint $table) {
            $table->smallIncrements(Category::COL_ID);
            $table->string(Category::COL_NAME);
            $table->string(Category::COL_COLOR);
        });
    }

    public function down()
    {
        Schema::dropIfExists(Category::TABLE);
    }
}