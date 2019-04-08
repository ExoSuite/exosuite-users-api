<?php

use App\Enums\Restriction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\Preferences;

class CreateProfileRestrictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_restrictions', function (Blueprint $table) {
            $table->uuid('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string('city')->default(Restriction::FRIENDS);
            $table->string('description')->default(Restriction::FRIENDS);
            $table->string('birthday')->default(Restriction::FRIENDS);
            $table->string('nomination_preference')->default(Preferences::FULL_NAME);
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
        Schema::dropIfExists('profile_restrictions');
    }
}
