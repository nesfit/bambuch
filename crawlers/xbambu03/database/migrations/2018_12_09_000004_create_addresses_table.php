<?php

use App\Models\Pg\Address;
use App\Models\Pg\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create(Address::TABLE, function (Blueprint $table) {
            $table->bigIncrements(Address::COL_ID);
            $table->string(Address::COL_ADDRESS);
            $table->integer(Address::COL_CRYPTO);
            $table->bigInteger(Address::COL_OWNER)->nullable();
            $table->string(Address::COL_COLOR, 8)->nullable();
            $table->timestamps();

            $table->index([Address::COL_ADDRESS]);
            $table->index([Address::COL_UPDATEDAT]);
        });

        Schema::create(Address::TABLE_CATEGORY, function (Blueprint $table)  {
            $table->bigInteger(Address::COL_ADDRID);
            $table->integer(Address::COL_CATID);

            $table->foreign(Address::COL_ADDRID)
                ->references(Address::COL_ID)
                ->on(Address::TABLE)
                ->onDelete('cascade');

            $table->foreign(Address::COL_CATID)
                ->references(Category::COL_ID)
                ->on(Category::TABLE)
                ->onDelete('cascade');

            $table->primary([Address::COL_ADDRID, Address::COL_CATID]);
        });


    }

    public function down()
    {
        Schema::dropIfExists(Address::TABLE_CATEGORY);
        Schema::dropIfExists(Address::TABLE);
    }
}