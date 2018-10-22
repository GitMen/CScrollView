<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wx_user extends Model
{
    //
    protected $table = 'wx_users';
    protected $primaryKey='wx_id';
    public $timestamps=false;
}
