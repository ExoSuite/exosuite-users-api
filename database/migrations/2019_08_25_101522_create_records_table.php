<?php declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('records', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('run_id');
            $table->foreign('run_id')->references('id')->on('runs')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('best_time');
            $table->uuid('best_time_user_run_id');
            $table->integer('sum_of_best');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->json('best_segments');
            $table->float('total_distance');
            $table->float('average_speed_on_best_time');
            $table->json('distance_between_cps');
            $table->json('best_speed_between_cps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
}
