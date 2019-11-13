<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\BoardPage;

class CreateBitcointalkMainTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(MainTopic::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean(MainTopic::COL_PARSED);
            $table->string(MainTopic::COL_URL, 256);
            $table->bigInteger(MainTopic::COL_BOARD_PAGE);

            $table
                ->foreign(MainTopic::COL_BOARD_PAGE)
                ->references(BoardPage::COL_ID)->on(BoardPage::TABLE)
                ->onDelete('cascade');

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
        Schema::dropIfExists('bitcointalk_main_topic');
    }
}
