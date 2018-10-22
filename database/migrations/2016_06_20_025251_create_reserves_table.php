<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserves', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->char('houses',50);
            $table->integer('unit')->unsigned();
            $table->integer('number')->unsigned();
            $table->boolean('payfor')->unsigned();
            $table->boolean('pay_bank')->unsigned();
            $table->date('reserve_time');
            $table->boolean('discount')->unsigned();
            $table->boolean('pay_status')->unsigned();
            $table->boolean('sign_zip')->unsigned();
            $table->boolean('reserve_class')->unsigned();
            $table->boolean('status')->unsigned();
            $table->boolean('progress')->unsigned();
            $table->string('special')->nullable();
            $table->string('notes')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reserves');
    }
}
