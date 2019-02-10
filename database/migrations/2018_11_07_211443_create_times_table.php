<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateTimesTable
 */
class CreateTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('times', function (Blueprint $table) {
            $table->uuid('id')->index();
            $table->unsignedTinyInteger('interval');
            $table->uuid('checkpoint_id');

            $table->foreign('checkpoint_id')
                ->references('id')
                ->on('check_points')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->uuid('run_id');

            $table->foreign('run_id')
                ->references('id')
                ->on('runs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('times');
    }
}
