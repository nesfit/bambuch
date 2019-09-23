<?php
use App\Models\Pg\Identity;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentiesTable extends Migration
{
    public function up()
    {
        Schema::create(Identity::TABLE, function (Blueprint $table) {
            $table->bigIncrements(Identity::COL_ID);
            $table->string(Identity::COL_URL, 1024);
            $table->text(Identity::COL_LABEL);
            $table->string(Identity::COL_SOURCE);
            $table->bigInteger(Identity::COL_ADDRID)->nullable();;

            $table->timestamps();

            $table->index([Identity::COL_URL]);
        });
    }

    public function down()
    {
        Schema::dropIfExists(Identity::TABLE);
    }
}