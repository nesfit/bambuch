<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\BitcointalkMainBoard;

class CreateBitcointalkMainBoardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(BitcointalkMainBoard::TABLE, function (Blueprint $table) {
            $table->bigIncrements(BitcointalkMainBoard::COL_ID);
            $table->string(BitcointalkMainBoard::COL_URL, 256);
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
