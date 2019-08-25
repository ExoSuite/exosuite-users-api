<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('run_id');
            $table->foreign('run_id')->references('id')->on('runs');
            $table->integer('best_time');
            $table->uuid('best_time_user_run_id');
            $table->integer('sum_of_best');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('best_segments');
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
        Schema::dropIfExists('records');
    }
}
