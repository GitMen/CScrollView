<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suggest extends Model
{
    protected $table = 'suggests';
    protected $primaryKey='id';
    public $timestamps=false;
}
