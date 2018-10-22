<?php

namespace App\Http\Controllers;

use App\Bank;
use EasyWeChat\Staff\Staff;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use response;
use App\User;
use App\Reserve;
use DB;

class TimeslotController extends Controller {

    // 保存
    public function saveTimeslot(Request $req) {

        if(!$req->data)
            response::jsonEncode(400, 'invalid data.');

        $ids = [];
        $datas = json_decode($req->data);
        foreach($datas as $k => $v) {
            if(isset($v->id))
                $ids[] = $v->id;
        }

        if(!$datas)
            DB::table('timeslots')->delete();

        if($ids)
            DB::table('timeslots')->whereNotIn('id', $ids)->delete();

        foreach($datas as $k => $v) {
            $time = time();
            if($v->id) {
                $datas = [
                    'started' => $v->started,
                    'ended' => $v->ended,
                    'updated' => $time,
                    'persons' => $v->persons,
                ];

                DB::table('timeslots')->where('id', $v->id)->update($datas);
            } else {
                $datas = [
                    'started' => $v->started,
                    'ended' => $v->ended,
                    'created' => $time,
                    'updated' => $time,
                    'persons' => $v->persons,
                ];

                DB::table('timeslots')->insert($datas);
            }
        }
        response::jsonEncode(200, 'success');
    }
    
    // 查询剩余预约时段列表
    public function readTimeslot(Request $req) {
        // $req->date = '2016-07-12';
        if(!$req->date)
            response::jsonEncode(400, 'invalid date.');

        $timeslot = DB::table('timeslots')->get();

        foreach($timeslot as $k => $v) {
            $row = DB::table('reserves_vs_timeslots')->where('date', '=', date('Y-m-d', strtotime($req->date)))->where('timeslot_id', '=', $v->id)->get();
            $timeslot[$k]->presence = count($row);
        }
        
        response::jsonEncode(200, 'success', $timeslot);
    }

    // 列表
    public function threadTimeslot(Request $req) {
        $timeslot = DB::table('timeslots')->get();

        response::jsonEncode(200, 'success', $timeslot);
    }
    //检测所选择的预约时间段剩余人数
    public function shengyuTimeslot(Request $req) {
//        $req->date=2016-07-19;
//        $req->timeslot_id=2;

        if( !$req->date && !$req->timeslot_id){
            response::jsonEncode(400, 'invalid date.');
        }
        $row = DB::table('reserves_vs_timeslots')->where('date', $req->date)->where('timeslot_id',$req->timeslot_id)->get();
        $data = [];
        foreach($row as $v){
            $data[]=$v->id;
        }
        if(!empty($data)){
            $count=count($data);
        }else{
            $count=0;
        }
        $timeslot = DB::table('timeslots')->where('id', '=', $req->timeslot_id)->first();
        $presence['num'] =$timeslot->persons-$count;

        response::jsonEncode(200, 'success', $presence);
    }
}
