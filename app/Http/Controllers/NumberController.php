<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Reserve;
use App\Number;
use App\Project;
use App\Bank;
use DB;
use response;

class NumberController extends Controller
{
    public function numberCompactSelect(Request $req){
        $number=$req->number;
        $numbers=Number::where('number',$number)->where('number_class',2)->where('number_push',0)->where('time',date('Y-m-d',time()))->first();
        if(!$numbers){
            response::jsonEncode(301,'error',['status'=>'号码无效']);
        }
        $reserve=Reserve::where('id',$numbers->reserve_id)->where('progress',4)->first();
        if(!$reserve){
            response::jsonEncode(302,'error',['status'=>'没有预约信息']);
        }
        $user=User::find($reserve->user_id);
        $project=Project::find($reserve->project_id);
        $data=[
            'reserve_id'   =>$reserve->id,
            'user_id'   =>$user['id'],
            'username'  =>$user['username'],
            'mobile'    =>$user['mobile'],
            'project'    =>$project->name,
            'unit'      =>$reserve->unit,
            'number'    =>$reserve->number,
            'payfor'    =>$reserve->payfor,
            'total_money'    =>$reserve->total_money,
            'first_money'    =>$reserve->first_money,
            'loan_money'    =>$reserve->loan_money,
            'pay_bank'  =>$reserve->pay_bank,
            'date'  =>$reserve->date,
            'reserve_stime' =>$reserve->reserve_stime,
            'reserve_etime' =>$reserve->reserve_etime,
            'discount'  =>$reserve->discount,
            'pay_status'   =>$reserve->pay_status,
            'sign_zip'  =>$reserve->sign_zip,
            'reserve_class'=>$reserve->reserve_class,
            //'reserve_class_notes'=>$reserve->reserve_class_notes,
            'status'    =>$reserve->status,
            'progress'    =>$reserve->progress,
            'special'   =>$reserve->special,
            'notes'     =>$reserve->notes,
        ];

        if($data){
            $code=200;
            $message='success';
            response::jsonEncode($code,$message,$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'查找失败']);

        }
    }
    public function numberSelect(Request $req){
        $number=$req->number;
        $numbers=Number::where('number',$number)->where('number_class',1)->where('number_push',0)->where('number_status',0)->where('time',date('Y-m-d',time()))->first();
        //dd($numbers);
        if(!$numbers){
            response::jsonEncode(301,'error',['status'=>'号码失效或搜索失败']);
        }
        $reserve=Reserve::where('id',$numbers->reserve_id)->where('progress','<',4)->first();
        //dd($reserve);
        if(!$reserve){
            response::jsonEncode(302,'error',['status'=>'没有预约信息']);
        }
        $user=User::find($reserve->user_id);
        $project=Project::find($reserve->project_id);
        $data=[
            'reserve_id'   =>$reserve->id,
            'user_id'   =>$user['id'],
            'username'  =>$user['username'],
            'mobile'    =>$user['mobile'],
            'project'    =>$project->name,
            'lou_fen'    =>$reserve->lou_fen,
            'lou_hao'    =>$reserve->lou_hao,
            'unit'      =>$reserve->unit,
            'number'    =>$reserve->number,
            'payfor'    =>$reserve->payfor,
            'total_money'    =>$reserve->total_money,
            'first_money'    =>$reserve->first_money,
            'loan_money'    =>$reserve->loan_money,
            'pay_bank'  =>$reserve->pay_bank,
            'date'  =>$reserve->date,
            'reserve_stime' =>$reserve->reserve_stime,
            'reserve_etime' =>$reserve->reserve_etime,
            'discount'  =>$reserve->discount,
            'pay_status'   =>$reserve->pay_status,
            'sign_zip'  =>$reserve->sign_zip,
            'reserve_class'=>$reserve->reserve_class,
            //'reserve_class_notes'=>$reserve->reserve_class_notes,
            'status'    =>$reserve->status,
            'progress'    =>$reserve->progress,
            'halt'    =>$reserve->halt,
            'special'   =>$reserve->special,
            'notes'     =>$reserve->notes,
        ];
        if($data){
            $code=200;
            $message='success';
            response::jsonEncode($code,$message,$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'查找失败']);

        }
    }
}
