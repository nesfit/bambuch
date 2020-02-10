<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\MainBoard;

class CreateBitcointalkMainBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(MainBoard::TABLE, function (Blueprint $table) {
            $table->bigIncrements(MainBoard::COL_ID);
            $table->boolean(MainBoard::COL_PARSED);
            $table->string(MainBoard::COL_URL, 256);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitcointalk_main_boards');
    }
}
