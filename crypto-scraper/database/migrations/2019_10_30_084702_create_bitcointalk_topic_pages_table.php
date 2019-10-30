<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\BitcointalkTopicPage;

class CreateBitcointalkTopicPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(BitcointalkTopicPage::TABLE, function (Blueprint $table) {
            $table->bigIncrements(BitcointalkTopicPage::COL_ID);
            $table->boolean(BitcointalkTopicPage::COL_PARSED);
            $table->boolean(BitcointalkTopicPage::COL_LAST);
            $table->string(BitcointalkTopicPage::COL_URL, 256);

            $table->timestamps();
            
            $table->index([BitcointalkTopicPage::COL_URL]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(BitcointalkTopicPage::TABLE);
    }
}
