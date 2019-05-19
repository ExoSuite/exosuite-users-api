<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\Restriction;

class UpdateDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("dashboards", function (Blueprint $table) {
            $table->string('visibility')->default(Restriction::FRIENDS_FOLLOWERS);
            $table->string('writing_restriction')->default(Restriction::FRIENDS);
            $table->dropColumn("restriction");
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
