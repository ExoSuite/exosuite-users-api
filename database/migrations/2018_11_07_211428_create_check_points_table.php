<?php

use Illuminate\Database\Migrations\Migration;
use Phaza\LaravelPostgis\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
/**
 * Class CreateCheckPointsTable
 */
class CreateCheckPointsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_points', function ($table) {
            /** @var Blueprint $table */
            $table->uuid('id')->primary();
            $table->string('type');
            $table->polygon('location');
            $table->uuid('run_id');
            $table->foreign('run_id')->references('id')->on('runs');
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
        Schema::dropIfExists('check_points');
    }
}
