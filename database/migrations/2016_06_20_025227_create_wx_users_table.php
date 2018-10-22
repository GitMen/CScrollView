<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWxUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_users', function (Blueprint $table) {
            $table->increments('wx_id');
            $table->char('wx_openid',50)->nullable();
            $table->char('wx_nick',20);
            $table->boolean('wx_sex')->unsigned();
            $table->string('wx_headimgurl');
            $table->boolean('status')->unsigned();
            $table->date('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('wx_users');
    }
}
