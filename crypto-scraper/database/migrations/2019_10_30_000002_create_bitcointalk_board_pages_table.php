<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\BoardPage;

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
            $table->string(BoardPage::COL_PARENT_URL, 256);
            
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
