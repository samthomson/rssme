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
        Schema::create('feed_userfeed', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feed_id');
            $table->integer('user_id');
            $table->string('name')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('feed_userfeed');
    }
}
