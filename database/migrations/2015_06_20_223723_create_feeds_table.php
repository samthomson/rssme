<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->string('name');
            $table->string('thumb');
            $table->integer('hit_count')->default(0);
            $table->integer('lastPulledCount')->default(0)->nullable->unsigned();
            $table->integer('item_count')->default(0);
            $table->dateTime('lastPulled')->nullable();
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
        Schema::drop('feeds');
    }
}
