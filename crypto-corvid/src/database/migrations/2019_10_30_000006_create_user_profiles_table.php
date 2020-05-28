<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Pg\Bitcointalk\UserProfile;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(UserProfile::TABLE, function (Blueprint $table) {
            $table->bigIncrements(UserProfile::COL_ID);
            $table->boolean(UserProfile::COL_PARSED);
            $table->string(UserProfile::COL_URL, 256);

            $table->timestamps();

            $table->index([UserProfile::COL_URL]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(UserProfile::TABLE);
    }
}
