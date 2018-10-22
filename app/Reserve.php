<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Reserve extends Model
{
    protected $table = 'reserves';
    protected $primaryKey='id';
    public $timestamps=false;

    public function phpSave($data = []) {
        $data['created'] = time();
        DB::table('reserves_vs_timeslots')->insert($data);
    }
    public function editTimeslot($id = 0 ,$data=[]) {
        DB::table('reserves_vs_timeslots')->where('id')->delete();
        DB::table('reserves_vs_timeslots')->insert($data);
    }
}
