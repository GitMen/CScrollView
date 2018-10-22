<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';
    protected $primaryKey='progress_id';
    public $timestamps=false;
}