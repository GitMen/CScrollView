<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wx_answer extends Model
{
    protected $table = 'wx_answers';
    protected $primaryKey='id';
    public $timestamps=false;
}
