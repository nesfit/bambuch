<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\TopicPage;

class CreateBitcointalkTopicPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(TopicPage::TABLE, function (Blueprint $table) {
            $table->bigIncrements(TopicPage::COL_ID);
            $table->boolean(TopicPage::COL_PARSED);
            $table->boolean(TopicPage::COL_LAST);
            $table->string(TopicPage::COL_URL, 256);
            $table->bigInteger(TopicPage::COL_MAIN_TOPIC)->nullable();

            $table->timestamps();
            
            $table->index([TopicPage::COL_URL]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(TopicPage::TABLE);
    }
}
