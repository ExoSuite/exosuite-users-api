<?php

use App\Enums\Restriction;
use App\Enums\Visibility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDashboardsTable
 */
class CreateDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id');
            $table->foreign('owner_id')->references('id')->on('users');
            //$table->string('visibility')->default(Restriction::FRIENDS_FOLLOWERS);
            //$table->string('writing_restriction')->default(Restriction::FRIENDS);
            $table->string('restriction')->default(Restriction::FRIENDS);

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
        Schema::dropIfExists('dashboards');
    }
}
