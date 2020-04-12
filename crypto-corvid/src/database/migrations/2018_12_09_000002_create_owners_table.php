<?php
use App\Models\Pg\Owner;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOwnersTable extends Migration
{
    public function up()
    {
        Schema::create(Owner::TABLE, function (Blueprint $table) {
            $table->bigIncrements(Owner::COL_ID);
            $table->string(Owner::COL_NAME);
            $table->string(Owner::COL_PLACEHOLDER)->nullable();
            $table->timestamps();

            $table->index([Owner::COL_NAME]);
        });
    }

    public function down()
    {
        Schema::dropIfExists(Owner::TABLE);
    }
}