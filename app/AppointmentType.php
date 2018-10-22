<?php
/**
 * Created by PhpStorm.
 * User: zhangdinghui
 * Date: 2017/11/6
 * Time: 下午1:18
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppointmentType extends Model
{

    protected $table = 'appointment_type';
    protected $primaryKey='id';
    public $timestamps=false;



}