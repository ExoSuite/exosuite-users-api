<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'user_profiles', function (Blueprint $table) {
            $table->uuid( 'id' )->index();
            $table->foreign( 'id' )->references( 'id' )->on( 'users' )->onDelete( 'cascade' );
            $table->date( 'birthday' )->nullable();
            $table->string( 'city' )->nullable();
            $table->text( 'description' )->nullable();
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'user_profiles' );
    }
}
