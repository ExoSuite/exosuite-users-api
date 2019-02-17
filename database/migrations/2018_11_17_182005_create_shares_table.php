<?php

use App\Models\Share;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUserSharesTable
 */
class CreateSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefix = Share::SHARE_RELATION_NAME;

        Schema::create('shares', function (Blueprint $table) use ($prefix) {
            $table->uuid('id');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->uuid("{$prefix}_id");
            $table->string("{$prefix}_type");

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
        Schema::dropIfExists('user_shares');
    }
}
