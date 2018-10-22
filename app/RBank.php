<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RBank extends Model
{
    protected $table = 'reserves_bank';
    protected $primaryKey='bank_id';
    public $timestamps=false;
}