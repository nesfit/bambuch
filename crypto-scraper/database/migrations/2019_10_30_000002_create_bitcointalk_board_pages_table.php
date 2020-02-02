<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\BoardPage;
use App\Models\Pg\Bitcointalk\MainBoard;

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
            $table->bigInteger(BoardPage::COL_MAIN_BOARD);

            $table
                ->foreign(BoardPage::COL_MAIN_BOARD)
                ->references(MainBoard::COL_ID)->on(MainBoard::TABLE)
                ->onDelete('cascade');
            
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
