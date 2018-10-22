<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class A_project extends Model
{
    protected $table = 'a_projects';
    protected $primaryKey='id';
    public $timestamps=false;
}
