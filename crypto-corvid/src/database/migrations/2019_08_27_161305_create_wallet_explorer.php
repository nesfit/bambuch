<?php
use App\Models\Pg\WalletExplorer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletExplorer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_explorer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string(WalletExplorer::COL_CATEGORY);
            $table->string(WalletExplorer::COL_OWNER);
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
        Schema::dropIfExists(WalletExplorer::TABLE);
    }
}
