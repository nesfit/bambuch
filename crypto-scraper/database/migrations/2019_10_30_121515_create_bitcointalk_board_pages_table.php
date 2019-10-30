<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\BitcointalkBoardPage;

class CreateBitcointalkBoardPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(BitcointalkBoardPage::TABLE, function (Blueprint $table) {
            $table->bigIncrements(BitcointalkBoardPage::COL_ID);
            $table->boolean(BitcointalkBoardPage::COL_PARSED);
            $table->boolean(BitcointalkBoardPage::COL_LAST);
            $table->string(BitcointalkBoardPage::COL_URL, 256);
            $table->bigInteger(BitcointalkBoardPage::COL_MAIN_BOARD);

            $table->timestamps();

            $table->index([BitcointalkBoardPage::COL_URL]);
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
