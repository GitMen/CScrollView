<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wx_note extends Model
{
    protected $table = 'wx_notes';
    protected $primaryKey='id';
    public $timestamps=false;
}
