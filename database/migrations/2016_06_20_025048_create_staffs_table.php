<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staffs', function (Blueprint $table) {
            $table->increments('id');
            $table->char('name',20);
            $table->char('mobile',15);
            $table->char('apartment_id',20);
            $table->char('role',20);
            $table->string('password',60);
            $table->date('time');
            $table->boolean('status')->unsigned();
            $table->boolean('delstatus')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('staffs');
    }
}
