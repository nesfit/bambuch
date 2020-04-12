<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Pg\Source;

class CreateSourcesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(Source::TABLE, function (Blueprint $table) {
            $table->id(Source::COL_ID);
            $table->string(Source::COL_NAME, 63);
            $table->string(Source::COL_URL, 127);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(Source::TABLE);
    }
}
