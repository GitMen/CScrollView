<?php

namespace App\Http\Controllers;

use App\A_project;
use App\Project;
use App\Suggest;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Excel;
use App\User;
use App\Wx_user;
use App\Wx_answer;
use App\Reserve;
use App\Number;
use App\Staff;
use App\Bank;
use response;
use xxtemp;

class FunController extends Controller
{
    //封装excel 下载预约详情的报表
    public function DownReserveProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $project_id = $project_id;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前预约表为空']);
        }
        $excelData=array_merge($excelData,$data);
        $head=$project->name."$start_date-$end_date 预约报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
               $sheet->rows($excelData);

            });
        })->export('xls');
    }

    public function getReserveProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id, $page = 1){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $project_id = $project_id;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $limit = 20;
            $reserve = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->get();
            $num=$reserve->count();
            $pageall=ceil($num/$limit);
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->forPage($page,$limit)->orderBy('id','desc')->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }
    //选择项目
    public function DownReserveProjectExcel(Request $req,$project_id){

        if($project_id) {
            $reserves = Reserve::where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        //dd($reserves);
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        //dd($data);
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前预约表为空']);
        }
        $excelData=array_merge($excelData,$data);
        $head=$project->name."-全部预约报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    //项目列表
    public function getReserveProjectExcel(Request $req,$project_id , $page = 1){
        if($project_id) {
            $limit = 20;
            $reserve = Reserve::where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->get();
            $num=$reserve->count();
            $pageall=ceil($num/$limit);
            $reserves = Reserve::where('project_id', $project_id)->whereIn('progress',[1,2,3,4,5])->forPage($page,$limit)->orderBy('id','desc')->get();

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }

        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前项目没有报表']);
        }

    }
    //=============================
    //封装excel 下载预约详情的报表
    public function DownReserveFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->whereIn('progress',[1,2,3,4,5])->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前预约表为空']);
        }
        $excelData=array_merge($excelData,$data);
        $head="$start_date-$end_date 全部项目预约报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);

            });
        })->export('xls');
    }
    public function getReserveFanweiExcel(Request $req,$reserve_stime,$reserve_etime ,$page = 1){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        $limit = 20;

        if($start_date && $end_date) {
            $reserve = Reserve::whereBetween('date', $date_fanwei)->whereIn('progress',[1,2,3,4,5])->get();
            $num=$reserve->count();
            $pageall=ceil($num/$limit);

            $reserves = Reserve::whereBetween('date', $date_fanwei)->whereIn('progress',[1,2,3,4,5])->forPage($page,$limit)->orderBy('id','desc')->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }


            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }

            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }


    protected static function banks($id){
        switch($id) {
            case 0:
                $bank_name = "无银行";
                break;
            case "1":
                $bank_name = "北京银行复兴支行";
                break;
            case "2":
                $bank_name = "兴业银行望京支行";
                break;
            case "3":
                $bank_name = "中信银行观湖国际支行";
                break;
            case "4":
                $bank_name = "杭州银行东城支行";
                break;
            case "5":
                $bank_name = "中国农业银行";
                break;
            case "6":
                $bank_name = "公积金兴丰苑";
                break;
            case "7":
                $bank_name = "公积金欣思达";
                break;
            case "8":
                $bank_name = "公积金阳光新桥";
                break;
            case "9":
                $bank_name = "其他银行";
                break;
            default:
                $bank_name = "无";
        }
        return $bank_name;
    }



    //=================end=========



    //封装excel 下载预约详情的报表
    public function DownReserveExcel(Request $req){
       set_time_limit (1000);
       //ini_set("memory_limit","-1");
       $reserves = Reserve::whereIn('progress',[1,2,3,4,5])->get();

       $data = [];
       $excelData=[
           ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
       ];
       foreach($reserves as $k=>$v){
           $project=Project::find($v->project_id);
           $user=User::find($v->user_id);
           if(!$user){
               continue;
           }
           //dd();

           if($v->isuser_bank_name == 1){
               if($v->bank_name != ''){
                   $bank_name=$v->bank_name;
               }else{
                   $bank_name='无';
               }
           }else{
               if($v->pay_bank != 0){
                   $bank_name=xxtemp::bank($v->pay_bank);
               }else{
                   $bank_name='无';
               }
           }
           switch($v->payfor){
               case 1:
                   $payfor = "一次性付款";
                   break;
               case 2:
                   $payfor = "银行按揭";
                   break;
               case 3:
                   $payfor = "公积金";
                   break;
               case 4:
                   $payfor = "组合贷";
                   break;
           }
           if($v->reserve_class == '1'){
               $status = "草签";
           }else if($v->reserve_class == '2'){
               $status = "正签";
           }else if($v->reserve_class == '3'){
               $status = "其他";
           }else{
               $status = $v->reserve_class;
           }
           $data[$k]=[
               'project_name'  =>$project->name,
               'lou_fen'        =>$v->lou_fen,
               'lou_hao'        =>$v->lou_hao,
               'unit'          =>$v->unit,
               'number'        =>$v->number,
               'user_name'     =>$user->username,
               'user_card'     => $user->card_id.' ',
               'user_mobile'   =>$user->mobile,
               'total_money'     =>$v->total_money,
               'payfor'  => $payfor,
               'first_money'       =>$v->first_money,
               'loan_money'       =>$v->loan_money,
               'pay_bank'       =>$bank_name,
               'discount'  => $v->discount==1?'是':"否",
               'pay_status'  => $v->pay_status==1?'是':"否",
               'sign_zip'  => $v->sign_zip==1?'是':"否",
               'special'  => $v->special,
               'notes'  => $v->notes,
               'status'  => $status,
               'date'  => $v->date,
               'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,
           ];
       }
       // dd($data);
       //var_dump($data);die();
       if(empty($data)){
           response::jsonEncode(301,'error',['status'=>'当前预约表为空']);
       }
       $excelData=array_merge($excelData,$data);

       Excel::create('预约详情报表',function($excel) use ($excelData) {
           $excel->sheet("sheet1",function($sheet) use ($excelData) {
               $sheet->setColumnFormat(array(
                   'G'=>'@',
//                   'H'=>'@',
               ));
               $sheet->rows($excelData);

           });
       })->export('xls');
    }
    //预约全部报表
    public function getReserveExcel(Request $req , $page = 1){
        $limit = 20;
        $reserves = Reserve::whereIn('progress',[1,2,3,4,5])->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','折扣是否已完成','销售变更是否已完成','客户签约及贷款资料是否齐全','车牌号','备注','签约类型','预约日期','预约时间']
        ];

        $reserves = Reserve::whereIn('progress',[1,2,3,4,5])->forPage($page,$limit)->orderBy('id','desc')->get();

        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($project){
                $project_name=$project->name;
//                $bank_name=xxtemp::banks($v->pay_bank);

                $bank_name= $v->isuser_bank_name == 1?$v->bank_name:xxtemp::bank($v->pay_bank);
            }else{
                $project_name='该预约没有房源/或为测试信息';
                $bank_name='无';
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }

            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project_name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'discount'  => $v->discount==1?'是':"否",
                'pay_status'  => $v->pay_status==1?'是':"否",
                'sign_zip'  => $v->sign_zip==1?'是':"否",
                'special'  => $v->special,
                'notes'  => $v->notes,
                'status'  => $status,
                'date'  => $v->date,
                'reserve_time'  => $v->reserve_stime.'-'.$v->reserve_etime,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有报表']);
        }

    }


    //+++++++++++++++++++++++++++++++++++++++预约已完成列表
    //封装excel 下载预约详情的报表
    public function DownReserveendProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];
        $project_name=Project::find($project_id);
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();

            //$cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
//            $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
//            if($hour<0){
//                $hour=0;
//            }
            $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
            $time=number_format($minute/60,2).'h';
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff->name,
                'cha_time'  => "$time",

            ];
        }
        $excelData=array_merge($excelData,$data);
        $head="$project->name $start_date-$end_date 预约完成报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getReserveendProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id , $page = 1){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $project_id = $project_id;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $reserve = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->get();
            $limit = 20;
            $num=$reserve->count();
            $pageall=ceil($num/$limit);
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->forPage($page,$limit)->orderBy('id','desc')->get();

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];
        //dd($reserves);
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
            if($number){
                $cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
            }else{
                $cha_time = 0;
            }
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff ? $sign_staff->name : '',
                'cha_time'  => $cha_time,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }

    //==================项目的完成预约状态
    public function DownReserveendProjectExcel(Request $req,$project_id){


        if($project_id) {
            $reserves = Reserve::where('project_id', $project_id)->where('progress','>',3)->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];

        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();

            //$cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
//            $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
//            if($hour<0){
//                $hour=0;
//            }
            $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
            $time=number_format($minute/60,2).'h';
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff->name,
                'cha_time'  => "$time",

            ];
        }
        $excelData=array_merge($excelData,$data);
        $head=$project->name." 预约已完成报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getReserveendProjectExcel(Request $req,$project_id , $page = 1){

        if($project_id) {
            $reserve = Reserve::where('project_id', $project_id)->where('progress','>',3)->get();
            $limit = 20;
            $num=$reserve->count();
            $pageall=ceil($num/$limit);
            $reserves = Reserve::where('project_id', $project_id)->where('progress','>',3)->forPage($page,$limit)->orderBy('id','desc')->get();

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];
        //dd($reserves);
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
            if($number){

                $cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
            }else{
                $cha_time = 0;
            }
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff ? $sign_staff->name : '',
                'cha_time'  => $cha_time,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }

    //==================时段
    public function DownReserveendFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->get();
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];

        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
//            $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
//            if($hour<0){
//                $hour=0;
//            }
            $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
            $time=number_format($minute/60,2).'h';
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff->name,
                'cha_time'  => "$time",

            ];
        }
        $excelData=array_merge($excelData,$data);
        $head="$start_date-$end_date 预约完成报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getReserveendFanweiExcel(Request $req,$reserve_stime,$reserve_etime , $page = 1){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;

        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date ) {
            $reserve = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->get();
            $limit = 20;
            $num=$reserve->count();
            $pageall=ceil($num/$limit);
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->forPage($page,$limit)->orderBy('id','desc')->get();

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];
        //dd($reserves);
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
            if($number){

                $cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
            }else{
                $cha_time = 0;
            }
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $sign_staff ? $sign_staff->name : '',
                'cha_time'  => $cha_time,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }

    //++++++++++++++++++++++++完成的全部
    public function DownReserveendExcel(Request $req){

        $reserves = Reserve::where('progress','>',3)->get();

        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];

        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            if($sign_staff){
                $staff_name=$sign_staff->name;
            }else{
                $staff_name='';
            }
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();

//            $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
//            if($hour<0){
//                $hour=0;
//            }
            $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
            $time=number_format($minute/60,2).'h';
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id.' ',
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $staff_name,
                'cha_time'  => "$time",

            ];
        }
        $excelData=array_merge($excelData,$data);
        $head="预约已完成报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->setColumnFormat(array(
                    'G'=>'@',
//                   'H'=>'@',
                ));
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getReserveendExcel(Request $req , $page = 1){

        $reserve = Reserve::where('progress','>',3)->get();
        $limit = 20;
        $num=$reserve->count();
        $pageall=ceil($num/$limit);
        $reserves = Reserve::where('progress','>',3)->forPage($page,$limit)->orderBy('id','desc')->get();

        $data=array();
        $excelData=[
            ['项目名称','楼盘分期','楼号','单元','房号','客户姓名','身份证号','联系电话','成交总价(元)','付款方式','首付款金额(元)','贷款金额(元)','贷款银行','预约日期','签约日期','签约类型','签约是否已完成','签约专员','签约时长']
        ];
        foreach($reserves as $k=>$v){
            $project=Project::find($v->project_id);
            $sign_staff=Staff::find($v->sign_staff_id);
            if($sign_staff){
                $staff_name=$sign_staff->name;
            }else{
                $staff_name='';
            }
            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
//dd($number);
            if($number){

                $cha_time=xxtemp::jetLag($number->star_time,$number->end_time);
            }else{
                $cha_time = 0;
            }
            $user=User::find($v->user_id);
            if(!$user){
                continue;
            }
            if($v->isuser_bank_name == 1){
                if($v->bank_name != ''){
                    $bank_name=$v->bank_name;
                }else{
                    $bank_name='无';
                }
            }else{
                if($v->pay_bank != 0){
                    $bank_name=xxtemp::bank($v->pay_bank);
                }else{
                    $bank_name='无';
                }
            }
            switch($v->payfor){
                case 1:
                    $payfor = "一次性付款";
                    break;
                case 2:
                    $payfor = "银行按揭";
                    break;
                case 3:
                    $payfor = "公积金";
                    break;
                case 4:
                    $payfor = "组合贷";
                    break;
            }
            if($v->reserve_class == '1'){
                $status = "草签";
            }else if($v->reserve_class == '2'){
                $status = "正签";
            }else if($v->reserve_class == '3'){
                $status = "其他";
            }else{
                $status = $v->reserve_class;
            }
            $data[$k]=[
                'project_name'  =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'user_name'     =>$user->username,
                'user_card'     =>$user->card_id,
                'user_mobile'   =>$user->mobile,
                'total_money'     =>$v->total_money,
                'payfor'  => $payfor,
                'first_money'       =>$v->first_money,
                'loan_money'       =>$v->loan_money,
                'pay_bank'       =>$bank_name,
                'reserve_date' => $v->date,
                'date'  => $v->date,
                'status'  => $status,
                'status_rs'  => '是',
                'sign_staff' => $staff_name,
                'cha_time'  => $cha_time,

            ];
        }
        if($data){
            $datas = [
                'num' => $num,
                'pageall' => $pageall,
                'data' => $data,
            ];
            response::jsonEncode(200,'success',$datas);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }





    //33333+++++++++++++++++++++++++++++++++++++++预约已完成列表
    //封装excel 按项目选择预约已完成的总结
    public function DownEndinfoProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        $project=Project::find($project_id);
        if($project){
            $project_name=$project->name;
        }else{
            $project_name='';
        }
        if($start_date && $end_date && $project_id) {
            //预约总数
            $reserves_all = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->count();
            if(!$reserves_all){
                $reserves_all=0;
            }
            //预约签约套数
            $reserves_end = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->where('reserve_center',0)->count();
            if(!$reserves_end){
                $reserves_end=0;
                $bili_reserves_end=0;
            }else{
                $bili_reserves_end=number_format(($reserves_end/$reserves_all),2);

            }

            //未预约签约套数
            $reserves_end_center = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->where('reserve_center',1)->count();
            if(!$reserves_end_center){
                $reserves_end_center=0;
                $bili_reserves_end_center=0;
            }else{
                $bili_reserves_end_center=number_format(($reserves_end_center/$reserves_all),2);

            }
            //签约完成率
            if($reserves_end && $reserves_all ){
                $bili_reserves=number_format((($reserves_end+$reserves_end_center)/$reserves_all),2);
            }else{
                $bili_reserves=0;
            }
            //取消预约套数
            $reserves_quxiao = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress',0)->where('status',2)->count();
            if(!$reserves_quxiao){
                $reserves_quxiao=0;
                $bili_reserves_quxiao=0;
            }else{
                $bili_reserves_quxiao=number_format(($reserves_quxiao/$reserves_all),2);
            }
            //到场未签约套数
            $reserves_guohao = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress',2)->whereIn('status',[1,3])->count();
            if(!$reserves_guohao){
                $reserves_guohao=0;
            }
            if($reserves_guohao){
                $bili_reserves_guohao=number_format(($reserves_guohao/$reserves_all),2);
            }else{
                $bili_reserves_guohao=0;
            }
            //平均预约时段
            $time_pj=0;
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->get();
            if(!$reserves){
                $time_pj=0;
            }
            $time_pj_arr=[];
            foreach($reserves as $k=>$v){
                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
                $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
                if($hour<0){
                    $hour=0;
                }
                $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
                $time_pj_arr[]=($hour*60)+$minute;
            }
            $num_time=count($time_pj_arr);
            $sum_time='';
            if($num_time){

                foreach($time_pj_arr as $k=>$v){
                    $sum_time+=$v;
                }
                $time_pj=floor($sum_time/$num_time);
            }else{
                $time_pj=0;

            }

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['序号','项目','预约签约数量','预约签约套数','预约签约比例','未预约签约套数','未预约签约比例','签约完成率','取消预约套数','取消预约比例','到场未签约套数','到场未签约比例','平均签约时长']
        ];


        $data[0] =[
            'a' =>1 ,
            'b' =>$project_name ,
            'c' => $reserves_all,
            'd' => $reserves_end,
            'e' =>$bili_reserves_end,
            'f' =>$reserves_end_center ,
            'g' =>$bili_reserves_end_center,
            'h' => $bili_reserves,
            'i' =>$reserves_quxiao ,
            'x' => $bili_reserves_quxiao,
            'k' =>$reserves_guohao ,
            'l' =>$bili_reserves_guohao,
            'n' => $time_pj,
        ];
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前预约表为空']);
        }
        $excelData=array_merge($excelData,$data);

        $head=$project->name."-全部报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getEndinfoProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        $project=Project::find($project_id);
        if($project){
            $project_name=$project->name;
        }else{
            $project_name='';
        }
        if($start_date && $end_date && $project_id) {
            //预约总数
            $reserves_all = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->count();
            if(!$reserves_all){
                $reserves_all=0;
            }
            //$bili_reserves_end='';
            //预约签约套数
            $reserves_end = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->where('reserve_center',0)->count();

            if(!$reserves_end){
                $reserves_end=0;
                $bili_reserves_end=0;
            }else{
                $bili_reserves_end=number_format(($reserves_end/$reserves_all),2);

            }

            //未预约签约套数
            $reserves_end_center = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->where('reserve_center',1)->count();
            if(!$reserves_end_center){
                $reserves_end_center=0;
                $bili_reserves_end_center=0;
            }else{
                $bili_reserves_end_center=number_format(($reserves_end_center/$reserves_all),2);

            }
            //签约完成率
            if($reserves_end && $reserves_all ){
                $bili_reserves=number_format((($reserves_end+$reserves_end_center)/$reserves_all),2);
            }else{
                $bili_reserves=0;
            }
            //取消预约套数
            $reserves_quxiao = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress',0)->where('status',2)->count();
            if(!$reserves_quxiao){
                $reserves_quxiao=0;
                $bili_reserves_quxiao=0;
            }else{
                $bili_reserves_quxiao=number_format(($reserves_quxiao/$reserves_all),2);
            }
            //到场未签约套数
            $reserves_guohao = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress',2)->whereIn('status',[1,3])->count();
            if(!$reserves_guohao){
                $reserves_guohao=0;
            }
            if($reserves_guohao){
                $bili_reserves_guohao=number_format(($reserves_guohao/$reserves_all),2);
            }else{
                $bili_reserves_guohao=0;
            }
            //平均预约时段
            $time_pj=0;
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('project_id', $project_id)->where('progress','>',3)->get();
            if(!$reserves){
                $time_pj=0;
            }
            $time_pj_arr=[];
            foreach($reserves as $k=>$v){
                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
                $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
                if($hour<0){
                    $hour=0;
                }
                $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
                $time_pj_arr[]=($hour*60)+$minute;
            }
            $num_time=count($time_pj_arr);
            $sum_time='';
            if($num_time){

                foreach($time_pj_arr as $k=>$v){
                    $sum_time+=$v;
                }
                $time_pj=floor($sum_time/$num_time);
            }else{
                $time_pj=0;

            }

        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['序号','项目','预约签约数量','预约签约套数','预约签约比例','未预约签约套数','未预约签约比例','签约完成率','取消预约套数','取消预约比例','到场未签约套数','到场未签约比例','平均签约时长']
        ];

        $data[0]=[
            '序号' =>1 ,
            '项目' =>$project_name ,
            '预约签约数量' => $reserves_all,
            '预约签约套数' => $reserves_end,
            '预约签约比例' =>$bili_reserves_end,
            '未预约签约套数' =>$reserves_end_center ,
            '未预约签约比例' =>$bili_reserves_end_center,
            '签约完成率' => $bili_reserves,
            '取消预约套数' =>$reserves_quxiao ,
            '取消预约比例' => $bili_reserves_quxiao,
            '到场未签约套数' =>$reserves_guohao ,
            '到场未签约比例' =>$bili_reserves_guohao,
            '平均签约时长' => $time_pj,
        ];
        if($data){
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }


    //封装excel 选择所有预约已完成的数据分析
    public function DownEndinfoFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date) {
            $reserve_project=Reserve::all();
            $project_num=[];
            foreach ($reserve_project as $k=>$v){
                $project_num []= $v->project_id;
            }
            $project_num=count(array_unique($project_num));
            $reserves_all = Reserve::whereBetween('date', $date_fanwei)->count();
            if(!$reserves_all){
                $reserves_all=0;
            }
            //$bili_reserves_end='';
            //预约签约套数
            $reserves_end = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->where('reserve_center',0)->count();
            if(!$reserves_end){
                $reserves_end=0;
                $bili_reserves_end=0;
            }else{
                $bili_reserves_end=number_format(($reserves_end/$reserves_all),2);
            }

            //未预约签约套数
            $reserves_end_center = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->where('reserve_center',1)->count();
            if(!$reserves_end_center){
                $reserves_end_center=0;
                $bili_reserves_end_center=0;
            }else{
                $bili_reserves_end_center=number_format(($reserves_end_center/$reserves_all),2);
            }
            if($reserves_end && $reserves_all ){
                $bili_reserves=number_format((($reserves_end+$reserves_end_center)/$reserves_all),2);
            }else{
                $bili_reserves=0;
            }
            //取消预约套数
            $reserves_quxiao = Reserve::whereBetween('date', $date_fanwei)->where('progress',0)->where('status',2)->count();
            if(!$reserves_quxiao){
                $reserves_quxiao=0;
                $bili_reserves_quxiao=0;
            }else{
                $bili_reserves_quxiao=number_format(($reserves_quxiao/$reserves_all),2);
            }
            //到场未签约套数
            $reserves_guohao = Reserve::whereBetween('date', $date_fanwei)->where('progress',2)->whereIn('status',[1,3])->count();
            if(!$reserves_guohao){
                $reserves_guohao=0;
            }
            if($reserves_guohao){
                $bili_reserves_guohao=number_format(($reserves_guohao/$reserves_all),2);
            }else{
                $bili_reserves_guohao=0;
            }
            //平均预约时段
            $time_pj=0;
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->get();
            if(!$reserves){
                $time_pj=0;
            }
            $time_pj_arr=[];
            foreach($reserves as $k=>$v){
                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
                if($number){
                    $hour=floor((strtotime($number->star_time)-strtotime($number->end_time))%86400/3600);
                    if($hour<0){
                        $hour=0;
                    }
                    $minute=floor((strtotime($number->end_time)-strtotime($number->star_time))%86400/60);
                }else{
                    $hour = 0;
                    $minute = 0;
                }

                $time_pj_arr[]=($hour*60)+$minute;
            }
            $num_time=count($time_pj_arr);
            $sum_time='';
            if($num_time){

                foreach($time_pj_arr as $k=>$v){
                    $sum_time+=$v;
                }
                $time_pj=floor($sum_time/$num_time);
            }else{
                $time_pj=0;

            }



        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['序号','项目','预约签约数量','预约签约套数','预约签约比例','未预约签约套数','未预约签约比例','签约完成率','取消预约套数','取消预约比例','到场未签约套数','到场未签约比例','平均签约时长']
        ];

        $data[0]=[
            '序号' =>1 ,
            '项目' =>$project_num ,
            '预约签约数量' => $reserves_all,
            '预约签约套数' => $reserves_end,
            '预约签约比例' =>$bili_reserves_end,
            '未预约签约套数' =>$reserves_end_center ,
            '未预约签约比例' =>$bili_reserves_end_center,
            '签约完成率' => $bili_reserves,
            '取消预约套数' =>$reserves_quxiao ,
            '取消预约比例' => $bili_reserves_quxiao,
            '到场未签约套数' =>$reserves_guohao ,
            '到场未签约比例' =>$bili_reserves_guohao,
            '平均签约时长' => $time_pj,
        ];
        $excelData=array_merge($excelData,$data);
        $head="$start_date-$end_date 报表";

        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getEndinfoFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date) {
            //项目总数
            $reserve_project=Reserve::all();
            $project_num=[];
            foreach ($reserve_project as $k=>$v){
                $project_num []= $v->project_id;
            }
            $project_num=count(array_unique($project_num));
            //预约总数
            $reserves_all = Reserve::whereBetween('date', $date_fanwei)->count();
            if(!$reserves_all){
                $reserves_all=0;
            }
            //$bili_reserves_end='';
            //预约签约套数
            $reserves_end = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->where('reserve_center',0)->count();
            if(!$reserves_end){
                $reserves_end=0;
                $bili_reserves_end=0;
            }else{
                $bili_reserves_end=number_format(($reserves_end/$reserves_all),2);
            }

            //未预约签约套数
            $reserves_end_center = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->where('reserve_center',1)->count();
            if(!$reserves_end_center){
                $reserves_end_center=0;
                $bili_reserves_end_center=0;
            }else{
                $bili_reserves_end_center=number_format(($reserves_end_center/$reserves_all),2);
            }
            if($reserves_end && $reserves_all){
                $bili_reserves=number_format((($reserves_end+$reserves_end_center)/$reserves_all),2);
            }else{
                $bili_reserves=0;
            }
            //取消预约套数
            $reserves_quxiao = Reserve::whereBetween('date', $date_fanwei)->where('progress',0)->where('status',2)->count();
            if(!$reserves_quxiao){
                $reserves_quxiao=0;
                $bili_reserves_quxiao=0;
            }else{
                $bili_reserves_quxiao=number_format(($reserves_quxiao/$reserves_all),2);
            }
            //到场未签约套数
            $reserves_guohao = Reserve::whereBetween('date', $date_fanwei)->where('progress',2)->whereIn('status',[1,3])->count();
            if(!$reserves_guohao){
                $reserves_guohao=0;
            }
            if($reserves_guohao){
                $bili_reserves_guohao=number_format(($reserves_guohao/$reserves_all),2);
            }else{
                $bili_reserves_guohao=0;
            }
            //平均预约时段
            $time_pj=0;
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress','>',3)->get();
            if(!$reserves){
                $time_pj=0;
            }
            $time_pj_arr=[];

            foreach($reserves as $k=>$v){

                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('number_status',1)->where('number_push',0)->first();
                if($number) {
                    $hour = floor((strtotime($number->star_time) - strtotime($number->end_time)) % 86400 / 3600);

                    if ($hour < 0) {
                        $hour = 0;
                    }
                    $minute = floor((strtotime($number->end_time) - strtotime($number->star_time)) % 86400 / 60);

                }else{
                    $hour = 0;
                    $minute = 0;
                }
                $time_pj_arr[] = ($hour * 60) + $minute;
            }
            $num_time=count($time_pj_arr);
            $sum_time='';
            if($num_time){

                foreach($time_pj_arr as $k=>$v){
                    $sum_time+=$v;
                }
                $time_pj=floor($sum_time/$num_time);
            }else{
                $time_pj=0;

            }



        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['序号','项目','预约签约数量','预约签约套数','预约签约比例','未预约签约套数','未预约签约比例','签约完成率','取消预约套数','取消预约比例','到场未签约套数','到场未签约比例','平均签约时长']
        ];

        $data=[
            '序号' =>1 ,
            '项目' =>$project_num ,
            '预约签约数量' => $reserves_all,
            '预约签约套数' => $reserves_end,
            '预约签约比例' =>$bili_reserves_end,
            '未预约签约套数' =>$reserves_end_center ,
            '未预约签约比例' =>$bili_reserves_end_center,
            '签约完成率' => $bili_reserves,
            '取消预约套数' =>$reserves_quxiao ,
            '取消预约比例' => $bili_reserves_quxiao,
            '到场未签约套数' =>$reserves_guohao ,
            '到场未签约比例' =>$bili_reserves_guohao,
            '平均签约时长' => $time_pj,
        ];
        if($data){
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }


//========================合同完成报表
    public function DownCompactProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $project=Project::find($project_id);
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress',5)->where('project_id', $project_id)->count();
            if(!$reserves){
                $reserves=0;
            }
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','合同领取数量']
        ];
        $data[0]=[
            'project'=>$project->name,
            'num'=>$reserves,
        ];
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前表为空']);
        }
        $excelData=array_merge($excelData,$data);
        $head=$project->name."$start_date-$end_date 合同领取报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getCompactProjectFanweiExcel(Request $req,$reserve_stime,$reserve_etime,$project_id){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $project=Project::find($project_id);
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date && $project_id) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress',5)->where('project_id', $project_id)->count();
            if(!$reserves){
                $reserves=0;
            }
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','合同领取数量']
        ];
        $data[0]=[
            'project'=>$project->name,
            'num'=>$reserves,
        ];
        if($data){
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }




    public function DownCompactFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date ) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress',5)->count();
            if(!$reserves){
                $reserves=0;
            }
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','合同领取数量']
        ];

        $data[] =[
            'project'=>'所有项目',
            'num'=>$reserves,
        ];
        if(empty($data)){
            response::jsonEncode(301,'error',['status'=>'当前表为空']);
        }
        $excelData=array_merge($excelData,$data);
        $head="所有项目 $start_date-$end_date 合同领取报表";
        Excel::create($head,function($excel) use ($excelData){
            $excel->sheet("sheet1",function($sheet)use ($excelData){
                $sheet->rows($excelData);
            });
        })->export('xls');
    }
    public function getCompactFanweiExcel(Request $req,$reserve_stime,$reserve_etime){
        $start_date = $reserve_stime;
        $end_date = $reserve_etime;
        $date_fanwei=[$start_date,$end_date];
        if($start_date && $end_date ) {
            $reserves = Reserve::whereBetween('date', $date_fanwei)->where('progress',5)->count();
            if(!$reserves){
                $reserves=0;
            }
        }else{
            response::jsonEncode(301,'error',['status'=>'参数不正确或者当前项目时间段没有预约信息']);
        }
        $data=array();
        $excelData=[
            ['项目名称','合同领取数量']
        ];
        $data=[
            'project'=>'所有项目',
            'num'=>$reserves,
        ];
        if($data){
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
        }

    }
















































    // //封装excel 显示预约到达率的报表列表


//    public function getReserveExceleee(Request $req,$reserve_stime,$reserve_etime,$project_id){
//        $start_date=$req->reserve_stime;
//        $end_date=$req->reserve_etime;
//        $project_id=$req->project_id;
//        $date_fanwei=[$start_date,$end_date];
//        if($start_date!=0 && $end_date!=0 && $project_id!=0){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->where('project_id',$project_id)->get();
//            dd($reserves);
//        }elseif($start_date && $end_date && $project_id=0){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->get();
//            dd($reserves);
//        }elseif($start_date=0 && $end_date=0 && $project_id){
//            $reserves=Reserve::where('project_id',$project_id)->get();
//            dd($reserves);
//        }else{
//            $reserves=Reserve::all();
//            //dd($reserves);
//        }
//        //dd($reserves);
//        $data=array();
//        $excelData=[
//            ['项目名称','单元','房号','客户姓名','预约日期','到场日期']
//        ];
//        foreach($reserves as $k=>$v){
//            $project=Project::find($v->project_id);
//            $user=User::find($v->user_id);
//            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('time',$v->date)->first();
//            //dd($number);
//            if(!$number){
////                response::jsonEncode(400,'error',['status'=>'没有到场人数']);
//
//                unset($data[$k]);
//            }else{
//                $data[$k]=[
//                    'project_name'  =>$project->name,
//                    'unit'          =>$v->unit,
//                    'number'        =>$v->number,
//                    'user_name'     =>$user->username,
//                    'reserve_date'  =>$v->date,
//                    'go_date'       =>$number->time,
//                ];
//            }
//
//        }
//        //dd($data);
//        if($data){
//            response::jsonEncode(200,'success',$data);
//        }else{
//            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
//        }
//
//    }
//    //封装excel 下载预约完成明细报表
//    public function DownReserveFindExcel(Request $req){
//        $start_date=$req->reserve_stime;
//        $end_date=$req->reserve_etime;
//        $project_id=$req->project_id;
//        $date_fanwei=[$start_date,$end_date];
//        if($start_date && $end_date && $project_id){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->where('project_id',$project_id)->where('progress',4)->get();
//            //dd($reserves);
//        }elseif($start_date && $end_date ){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->where('progress',4)->get();
//            //dd($reserves);
//        }else{
//            $reserves=Reserve::all();
//        }
//        $data=array();
//        $excelData=[
//            ['项目名称','单元','房号','客户姓名','身份证号','联系电话']
//        ];
//        foreach($reserves as $k=>$v){
//            $project=Project::find($v->project_id);
//            $user=User::find($v->user_id);
//            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('time',$v->date)->first();
//            //dd($number);
//            if(!$number){
////                response::jsonEncode(400,'error',['status'=>'没有到场人数']);
//
//                unset($data[$k]);
//            }else{
//                $data[$k]=[
//                    'project_name'  =>$project->name,
//                    'unit'          =>$v->unit,
//                    'number'        =>$v->number,
//                    'user_name'     =>$user->username,
//                    'user_card'  =>$user->card_id,
//                    'user_mobile'       =>$user->mobile,
//                ];
//            }
//
//        }
//        //dd($data);
////        for($i=0;$i<count($data);$i++){
////            $mergedata=[$data[$i]['project_name'],$data[$i]['unit'],$data[$i]['number'],$data[$i]['user_name'],$data[$i]['reserve_date'],$data[$i]['go_date']];
////        }
//        $excelData=array_merge($excelData,$data);
//        //dd($excelData);
//        //$head=iconv('GBK','UTF-8','学生成绩');
//        $head="$start_date-$end_date 预约到达率报表";
//        Excel::create($head,function($excel) use ($excelData){
//            $excel->sheet("sheet1",function($sheet)use ($excelData){
//                $sheet->rows($excelData);
//            });
//        })->export('xls');
//    }
//    // //封装excel 显示预约到达率的报表列表
//    public function getReserveFindExcel(Request $req){
//        $start_date=$req->reserve_stime;
//        $end_date=$req->reserve_etime;
//        $project_id=$req->project_id;
//        $date_fanwei=[$start_date,$end_date];
//        if($start_date && $end_date && $project_id){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->where('project_id',$project_id)->where('progress',4)->get();
//            //dd($reserves);
//        }elseif($start_date && $end_date ){
//            $reserves=Reserve::whereBetween('date',$date_fanwei)->where('progress',4)->get();
//            //dd($reserves);
//        }else{
//            $reserves=Reserve::all();
//        }
//        //dd($reserves);
//        $data=array();
//        $excelData=[
//            ['项目名称','单元','房号','客户姓名','预约日期','到场日期']
//        ];
//        foreach($reserves as $k=>$v){
//            $project=Project::find($v->project_id);
//            $user=User::find($v->user_id);
//            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->where('time',$v->date)->first();
//            //dd($number);
//            if(!$number){
////                response::jsonEncode(400,'error',['status'=>'没有到场人数']);
//
//                unset($data[$k]);
//            }else{
//                $data[$k]=[
//                    'project_name'  =>$project->name,
//                    'unit'          =>$v->unit,
//                    'number'        =>$v->number,
//                    'user_name'     =>$user->username,
//                    'user_card'  =>$user->card_id,
//                    'user_mobile'       =>$user->mobile,
//                ];
//            }
//
//        }
//        //dd($data);
//        if($data){
//            response::jsonEncode(200,'success',$data);
//        }else{
//            response::jsonEncode(400,'error',['status'=>'当前指定日期没有报表']);
//        }
//
//    }
//
//
//    public function endReserveExcel(Request $req){
//        $start_date=$req->start_date;
//        $end_date=$req->end_date;
//        $reserves=Reserve::where('date','>',$end_date)->where('date','<',$start_date)->get();
//        //dd($reserves);
//        $data=array();
//        $excelData=[
//            ['项目名称','单元','房号','客户姓名','身份证号','联系电话']
//        ];
//        foreach($reserves as $k=>$v){
//            $project=Project::find($v->project_id);
//            $user=User::find($v->user_id);
//            $number=Number::where('reserve_id',$v->id)->where('number_class',1)->first();
//            if(!$number){
//                response::jsonEncode(400,'error',['status'=>'没有到场人数']);
//            }
//            $data[$k]=[
//                'project_name'  =>$project->name,
//                'unit'          =>$v->unit,
//                'number'        =>$v->number,
//                'user_name'     =>$user->username,
//                'user_card'  =>$user->card_id,
//                'user_mobile'       =>$user->mobile,
//            ];
//        }
//        //dd($data);
//
//
////        for($i=0;$i<count($data);$i++){
////            $mergedata=[$data[$i]['project_name'],$data[$i]['unit'],$data[$i]['number'],$data[$i]['user_name'],$data[$i]['reserve_date'],$data[$i]['go_date']];
////
////
////        }
//        $excelData=array_merge($excelData,$data);
//        //$head=iconv('GBK','UTF-8','学生成绩');
//        $head="$start_date-$end_date 预约到达率报表";
//        Excel::create($head,function($excel) use ($excelData){
//            $excel->sheet("sheet1",function($sheet)use ($excelData){
//                $sheet->rows($excelData);
//            });
//        })->export('xls');
//    }
    //设置时间
    //短信找回密码
    public function getCode(Request $req){
        //service provider
        //$card=$req->card;用户用什么登录,手机号登录还是 工号登录
        require(base_path()."/vendor/alidayu/TopSdk.php");
        $code=mt_rand(100000,999999);
        $req->session()->put('code',$code);
        $mobile='15292320376';
        $c = new \TopClient;
        $c->appkey = '23360630' ;
        $c->secretKey = '828ffe903783b300871fe2745b4036df';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("西游却东行");
        $req->setSmsParam("{\"num\":\"$code\"}");
        $req->setRecNum("$mobile");
        $req->setSmsTemplateCode("SMS_11440239");
        //var_dump($req);exit;
        //$resp = $c->execute($req);
    }
    //投诉添加
    public function suggestAdd(){

    }
    //建议全部列表
    public function suggestList(Request $req,$page=1){
        $limit=15;
        $suggestall=Suggest::all();
        if(!$suggestall){
            response::jsonEncode(400,'error',['status'=>'当前没有建议信息']);
        }
        $num=$suggestall->count();
        $pageall=ceil($num/$limit);
        //dd($suggestall);
        $suggest_page=Suggest::forPage($page,$limit)->orderBy('id','desc')->get();
        if($suggest_page) {
            foreach ($suggest_page as $k => $v) {
                $user = User::where('openid', $v->openid)->first();
                if (!$user) {
                    $wx_user = Wx_user::where('wx_openid', $v->openid)->first();
                    $name = '微信昵称:'.$wx_user->wx_nick;
                    $mobile = '无记录';
                    $staff_name = '无记录';
                    $project = '无记录';

                } else {
                    $staff = Staff::find($user->staff_id);
                    $reserve = Reserve::where('user_id', $user->id)->first();
                    if (!$reserve) {
                        $name = $user->username;
                        $mobile = $user->mobile;
                        $staff_name = $staff->name;
                        $project = '无记录';
                    } else {
                        $name = $user->username;
                        $mobile = $user->mobile;
                        $staff_name = $staff->name;
                        $projects = Project::find($reserve->project_id);
                        $project = $projects->name;
                    }
                }
                $suggests[] = [
                    'name' => $name,
                    'mobile' => $mobile,
                    'staff_name' => $staff_name,
                    'project' => $project,
                    'content' => $v->contents,
                    'date' => $v->create_time,
                ];

            }
            if (empty($suggests)) {
                response::jsonEncode(400, 'error', ['status' => '当前页码没有建议']);
            }
            $date = [
                'num' => $num,
                'pageall' => $pageall,
                'suggest' => $suggests,
            ];
            response::jsonEncode(200, 'success', $date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前页码没有建议']);
        }
    }
    //投诉 按照 项目 分组的信息
    public function suggestProjectList(Request $req,$page=1){
        $limit=15;
        $project_id=$req->project_id;
        //dd($project_id);
        $suggestall=Suggest::where('project_id',$project_id)->get();
        if(count($suggestall)==0){
            response::jsonEncode(400,'error',['status'=>'当前项目没有建议信息']);
        }
        $num=$suggestall->count();
        $pageall=ceil($num/$limit);
        //dd($suggestall);
        $suggest_page=Suggest::where('project_id',$project_id)->orderBy('id','desc')->get();
        //dd($suggest_page);
        if($suggest_page) {
            foreach ($suggest_page as $k => $v) {
                $user = User::where('openid', $v->openid)->first();
                $staff = Staff::find($user->staff_id);
                $reserve = Reserve::where('user_id', $user->id)->first();
                $name = $user->username;
                $mobile = $user->mobile;
                $staff_name = $staff->name;
                $projects = Project::find($reserve->project_id);
                $project = $projects->name;
                $suggests[] = [
                    'name' => $name,
                    'mobile' => $mobile,
                    'staff_name' => $staff_name,
                    'project' => $project,
                    'content' => $v->contents,
                    'date' => $v->create_time,
                ];

            }
            if (empty($suggests)) {
                response::jsonEncode(400, 'error', ['status' => '当前页码没有信息']);
            }
            $date = [
                'num' => $num,
                'pageall' => $pageall,
                'suggest' => $suggests,
            ];
            response::jsonEncode(200, 'success', $date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前项目没有建议信息']);
        }
    }


    //自定义问答
    public function wxAnswersAdd(Request $req){
        $question=$req->question;
        $question=str_replace('。','.',$question);
        $answer=$req->answer;
        if($question && $answer){
            $wx_answer=new Wx_answer();
            $wx_answer->keywords=$question;
            $wx_answer->answers=$answer;
            $wx_answer->num=0;
            $wx_answer->create_time=date('Y-m-d',time());
            $wx_answer->save();
            $num=$wx_answer->id;
            //分隔插入保证查询成功
            $ex_data=explode('.',$question);
            $ex_data=array_unique($ex_data);
            $ex_data=array_filter($ex_data);
            //dd($ex_data);
            foreach($ex_data as $k=>$v){
                $wx_answer=new Wx_answer();
                $wx_answer->keywords=$v;
                $wx_answer->answers=$answer;
                $wx_answer->create_time=date('Y-m-d',time());
                $wx_answer->num=$num;
                $wx_answer->save();
            }
            response::jsonEncode(200,'success',['status'=>'用户插入成功']);

        }else{
            response::jsonEncode(400,'error',['status'=>'用户没有输入问题或者回答']);
        }
    }
    //list
    public function wxAnswersList(Request $req,$page=1){
        $limit=15;
        $wx_answerall=Wx_answer::where('num',0)->get();
        if(!$wx_answerall){
            response::jsonEncode(400,'error',['status'=>'当前没有建议信息']);
        }
        $num=$wx_answerall->count();
        $pageall=ceil($num/$limit);
        $wx_answers=Wx_answer::where('num',0)->forPage($page,$limit)->orderBy('id','desc')->get();
        if($wx_answers){
            foreach($wx_answers as $k=>$v){
                $wx_rs[]=[
                    'wx_answers_id' => $v->id,
                    'keywords' => $v->keywords,
                    'answers' => $v->answers,
                    'create_time' => $v->create_time,
                ];
            }
            if(!empty($wx_rs)){
                $data = [
                    'num' => $num,
                    'pageall' => $pageall,
                    'wx_answers' => $wx_rs,
                ];
                response::jsonEncode(200, 'success', $data);
            }else{

                response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
            }

        }else{
            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }
    }
    //查找回答
    public function wxAnswersSelect(Request $req){
        $wx_answers_id=$req->id;
        $wx_answer=Wx_answer::where('id',$wx_answers_id)->where('num',0)->first();
        if($wx_answer){
                $wx_rs=[
                    'wx_answers_id' => $wx_answer->id,
                    'keywords' => $wx_answer->keywords,
                    'answers' => $wx_answer->answers,
                ];
            response::jsonEncode(200,'success',$wx_rs);
        }else{
            response::jsonEncode(400,'error',['status'=>'没有获取到主键id']);
        }
    }
    //修改回答
    public function wxAnswersEdit(Request $req){
        $wx_answers_id=$req->id;
        $question=$req->question;
        $answer=$req->answer;

        $wx_answer=Wx_answer::where('id',$wx_answers_id)->where('num',0)->first();
        if(!$wx_answer){
            response::jsonEncode(200,'success',['status'=>'没获取到主键']);
        }
        $num=$wx_answer->id;
        $del_id=Wx_answer::where('id',$wx_answers_id)->delete();
        $del_num=Wx_answer::where('num',$num)->delete();
        if($del_id && $del_num){
            if($question && $answer){
                $wx_answer=new Wx_answer();
                $wx_answer->keywords=$question;
                $wx_answer->answers=$answer;
                $wx_answer->create_time=date('Y-m-d',time());
                $wx_answer->num=0;
                $wx_answer->save();
                $num=$wx_answer->id;
                //分隔插入保证查询成功
                $ex_data=explode('.',$question);
                $ex_data=array_unique($ex_data);
                $ex_data=array_filter($ex_data);
                foreach($ex_data as $k=>$v){
                    $wx_answer=new Wx_answer();
                    $wx_answer->keywords=$v;
                    $wx_answer->answers=$answer;
                    $wx_answer->num=$num;
                    $wx_answer->save();
                }
                response::jsonEncode(200,'success',['status'=>'用户插入成功']);

            }else{
                response::jsonEncode(400,'error',['status'=>'用户没有输入问题或者回答']);
            }

            response::jsonEncode(200,'success',['status'=>'删除成功']);
        }
    }
    //删除问答
    public function wxAnswersDelete(Request $req){
        $wx_answers_id=$req->id;
        $wx_answer=Wx_answer::where('id',$wx_answers_id)->where('num',0)->first();
        if(!$wx_answer){
            response::jsonEncode(400,'error',['status'=>'不要重复删除']);
        }
        $num=$wx_answer->id;
        $del_id=Wx_answer::where('id',$wx_answers_id)->delete();
        $del_num=Wx_answer::where('num',$num)->delete();
        if($del_id && $del_num){
            response::jsonEncode(200,'success',['status'=>'删除成功']);
        }else{
            response::jsonEncode(400,'error',['status'=>'不要重复删除']);
        }
    }
}
