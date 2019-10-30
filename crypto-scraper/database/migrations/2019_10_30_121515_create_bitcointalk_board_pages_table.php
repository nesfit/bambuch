<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\BoardPage;

class CreateBitcointalkBoardPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(BoardPage::TABLE, function (Blueprint $table) {
            $table->bigIncrements(BoardPage::COL_ID);
            $table->boolean(BoardPage::COL_PARSED);
            $table->boolean(BoardPage::COL_LAST);
            $table->string(BoardPage::COL_URL, 256);
            $table->bigInteger(BoardPage::COL_MAIN_BOARD)->nullable();
            
            $table->timestamps();

            $table->index([BoardPage::COL_URL]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitcointalk_board_pages');
    }
}
