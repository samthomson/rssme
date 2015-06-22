<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeditemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeditems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feed_id');
            $table->string('title');
            $table->string('url');
            $table->string('guid');
            $table->dateTime('pubDate');
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
        Schema::drop('feeditems');
    }
}
