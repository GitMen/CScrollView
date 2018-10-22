<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    //
    protected $table = 'staffs';
    protected $primaryKey='id';
    public $timestamps=false;
}
