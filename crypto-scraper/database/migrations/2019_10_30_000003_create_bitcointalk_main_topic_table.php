<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\MainTopic;
use App\Models\Pg\Bitcointalk\BoardPage;

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
            $table->bigIncrements(MainTopic::COL_ID);
            $table->boolean(MainTopic::COL_PARSED);
            $table->string(MainTopic::COL_URL, 256);
            $table->string(MainTopic::COL_PARENT_URL, 256);

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
