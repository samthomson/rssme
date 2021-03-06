<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserfeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feed_id');
            $table->integer('user_id');
            $table->string('name')->default('');
            $table->string('colour', 6)->default('')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('feed_user');
    }
}
