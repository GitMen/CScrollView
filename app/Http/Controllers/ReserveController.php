<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Project;
use App\RBank;
use App\RTime;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\User;
use App\Reserve;
use App\Staff;
use App\Number;
use Illuminate\Support\Facades\DB;
use response;
use xxtemp;
class ReserveController extends Controller {
    //一键过号今天所有取号没来,没有到预约中心的预约信息
    public function keyGuohao(){
        $time=date('H');
        $date=date('Y-m-d');
        $data=[];
        if($time>=0){
            $reserves=Reserve::where('date',$date)->whereIn('progress',[1,3])->get();
            foreach($reserves as $k=>$v){
                $v->progress=2;
                $v->save();
                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->first();
                if($number){
                    $number->number_push=1;
                    $number->number_status=0;
                    $number->save();
                }
                $data[$k]=['status'=>"$v->id 过号成功"];
            }
            if($data){
                response::jsonEncode(200,'success',$data);

            }else{
                response::jsonEncode(401,'error',['status'=>'没有预约要过号']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'没有到时间']);

        }
    }
    //超级管理员删除预约
    public function reserveAdminDel(Request $req){
        $id=$req->reserve_id;
        $reserve=Reserve::where('id',$id)->delete();
        $row = DB::table('reserves_vs_timeslots')->where('reserve_id', '=', $id)->delete();
        $number=Number::where('reserve_id',$id)->delete();
        if($reserve ){
            response::jsonEncode(200,'success',['status'=>'删除成功']);
        }else{
            response::jsonEncode(400,'error',['status'=>'删除失败']);
        }
    }
    //检查房源唯一
    public function getReserveOnly(Request $req){
        $staff_id=$req->staff_id;
        $staff=Staff::find($staff_id);
        $project_id=$staff->project_id;
        $lou_fen=trim($req->lou_fen);
        $lou_hao=trim($req->lou_hao);
        //$lou_hao=1;
        $unit=trim($req->unit);
        //$unit=1;
        $number=trim($req->number);

        if($project_id && (isset($lou_fen) || $lou_fen) && (isset($lou_hao) ||$lou_hao) && (isset($unit)||$unit) && (isset($number)||$number)){
            $reserve=Reserve::where('project_id',$project_id)->where('lou_fen',$lou_fen)->where('lou_hao',$lou_hao)->where('unit',$unit)->where('number',$number)->first();
            //dd($reserve);
            if($reserve){
                response::jsonEncode(400,'error',['status'=>'房源不能或已被预约']);
            }else{
                response::jsonEncode(200,'success',['status'=>'房源可以预约']);
            }
        }else{
            response::jsonEncode(401,'error',['status'=>'检测信息不完全']);
        }
    }

    public function reserveList(Request $req ,$staff_id, $page=1){
        $limit=10;

        $reserves=Reserve::where('staff_id',$staff_id)->forPage($page,$limit)->orderBy('id','desc')->get();
        $reserve=Reserve::where('staff_id',$staff_id)->get();
        $num=$reserve->count();
        $pageall=ceil($num/$limit);

        foreach($reserves as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

            }
        if(!empty($data)){

            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }
//顾问成员查看自己所属用户的项目预约列表200
    public function reserveStarProjectList(Request $req ,$staff_id,$project_id,$page){
        $limit=10;
        $reserves=Reserve::where('staff_id',$req->staff_id)->where('project_id',$req->project_id)->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('staff_id',$req->staff_id)->where('project_id',$req->project_id)->forPage($page,$limit)->orderBy('id','desc')->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){

            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }
    //超级管理员查看的预约列表 分页OK
    public function reserveAdminList(Request $req , $page=1){
        $limit=10;
        $reserve=Reserve::all();
        $num=$reserve->count();
        //dd($num);
        $pageall=ceil($num/$limit);
        $reserves=Reserve::forPage($page,$limit)->orderBy('date','desc')->get();
        foreach($reserves as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);

            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date'       => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){

            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }


    //超级管理员查看的项目预约列表
    public function reserveStarAdminList(Request $req ,$project_id,$page){
        $reserves=Reserve::where('project_id',$req->project_id)->get();
        $limit=10;
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('project_id',$req->project_id)->forPage($page,$limit)->orderBy('id','desc')->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date'       => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }


    public function findAllLUser(){
        return User::where('class',3)->where('ismain',0)->get();
    }

    //顾问管理员查看所属成员的预约列表
    public function reserveAdminsList(Request $req,$admin_id,$page){
        $limit=10;
        //查询管理员所属员工数组
        $skip=($page-1)*$limit;
        $staffs=Staff::where('pid',$req->admin_id)->get();

        foreach($staffs as $k=>$v){
            $staff[]=$v->id;
        }
        if(empty($staff)){
            response::jsonEncode(400,'error',['status'=>'该管理员下没有员工']);
        }
        $reserves=Reserve::whereIn('staff_id',$staff)->skip($skip)->take($limit)->orderBy('id','desc')->get();
        $reserves_all=Reserve::whereIn('staff_id',$staff)->get();
        $num=$reserves_all->count();
        $pageall=ceil($num/$limit);

        $allJUsers = $this->findAllLUser();

        foreach($reserves as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);
            }
            $jUsers = array();
            //查询关联客户
            if($user->ismain == 1 && $user->class == 3){
                foreach ($allJUsers as $u){
                    if($u->main_user_id == $user->id){
                        $jUsers[] =  $u;
                    }
                }
            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
                'jUsers' => $jUsers
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }

    //顾问管理员查看所属成员的 项目 预约列表
    public function reserveStarAdminsList(Request $req,$admin_id,$project_id,$page){
        
        $staffs=Staff::where('pid',$req->admin_id)->get();
        foreach($staffs as $k=>$v){
            $staff[]=$v->id;
        }
        if(empty($staff)){
            response::jsonEncode(400,'error',['status'=>'该管理员下没有员工']);
        }
        $reserves=Reserve::whereIn('staff_id',$staff)->where('project_id',$req->project_id)->get();
        $limit=10;
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::whereIn('staff_id',$staff)->where('project_id',$req->project_id)->orderBy('id','desc')->skip(($page-1)*$limit)->take($limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'    =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前页码没有信息']);
        }

    }

    //循环项目列表
    public function projectList(){
        $projects=Project::all();
        foreach($projects as $k=>$v){
            $project[]=[
                'project_id' => $v->id,
                'project_name' => $v->name,
            ];
        }
        if(!empty($project)){
            response::jsonEncode(200,'success',$project);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有项目']);
        }
    }
    //选择预约已完成的项目列表  推送 合同领取通知
    public function reserveProjectList($project_id,$page=1){
        $limit=10;
        $reserves=Reserve::where('project_id',$project_id)->where('progress','>',3)->orderBy('id','desc')->where('compact_notes',0)->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('project_id',$project_id)->where('progress','>',3)->orderBy('id','desc')->where('compact_notes',0)->forPage($page,$limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);

            }
            $data[$k]=[
                'reserve_id'            =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前没有信息']);
        }

    }

    public function reserveProjectListNoCam($project_id,$page=1){
        $limit=10;
        $reserves=Reserve::where('project_id',$project_id)->where('progress','<',4)->where('status','!=',4)->orderBy('date','desc')->where('compact_notes',0)->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('project_id',$project_id)->where('progress','<',4)->where('status','!=',4)->orderBy('date','desc')->where('compact_notes',0)->forPage($page,$limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(400,'error',['status'=>'关联项目失败']);

            }
            $data[$k]=[
                'reserve_id'            =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{

            response::jsonEncode(400,'error',['status'=>'当前没有信息']);
        }

    }


    //发送预约完成领取合同消息
    public function reserveGetCompact(Request $req){
        $ids=$req->ids;
        //$ids=[12,13,14,15,19,20];
        $data=[];
        foreach($ids as $k=>$v){
            $reserve=Reserve::find($v);
            $project=Project::find($reserve->project_id);
            $hourse=$project->name.' '.$reserve->lou_fen.'-'.$reserve->lou_hao.','.$reserve->unit.'-'.$reserve->number;
            if(!$reserve){
                $data[$k]=[301,'error',['status'=>"$hourse 没有预约信息"]];
                //response::jsonEncode(301,'error',['status'=>'没有预约信息']);
            }else{
                $reserve->compact_notes=1;
                if($reserve->save()){
                    $wx=new WxController();
                    $notice=$wx->noticeMB($reserve->id);
                    if($notice==1) {
                        $data[$k]=[200,'success',['status'=>"$hourse 微信通知推送成功"]];
//                    $data[$k]['status']="$v 没有预约信息";
//                    $data[$k]['status']="$v 没有预约信息";
//                    response::jsonEncode(200, 'success', ['status' => '发送并推送成功']);
                    }else{
                        $data[$k]=[200,'success',['status'=>"$hourse 发送成功,但该用户没有绑定微信"]];
//                    response::jsonEncode(200, 'success', ['status' => '发送成功']);
                    }
                }else{
                    $data[$k]=[400,'success',['status'=>"$hourse 发送失败"]];
//                response::jsonEncode(400,'error',['status'=>'发送失败']);
                }
            }
            //dd($project);
        }
        response::jsonEncode(200,'success',$data);

    }
    //选择预约已完成未发消息的的列表
    public function reserveProgressGetCompactList($page){
        $limit=10;
        $reserves=Reserve::where('progress','=',4)->where('compact_notes',0)->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('progress','=',4)->where('compact_notes',0)->forPage($page,$limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(401,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'            =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有已完成预约的信息']);
        }

    }

    public function reserveProgressGetUnCompactList($page){
        $limit=10;
        $reserves=Reserve::where('progress','<',4)->where('compact_notes',0)->where('status','!=',4)->get();
        $num=$reserves->count();
        $pageall=ceil($num/$limit);
        $reserve=Reserve::where('progress','<',4)->where('compact_notes',0)->where('status','!=',4)->orderBy('date', 'desc')->forPage($page,$limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(401,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'            =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'progress'       =>$v->progress,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有已完成预约的信息']);
        }
    }


    //选择预约已完成的列表reserveProgressEndList   最后  领取合同完成的列表  最后一个微信推送
    public function reserveProgressEndList($page=1){
        //$reserves=Reserve::where('progress','>',3)->get();
        $reserves=Reserve::where('progress','=',4)->where('compact_notes',1)->where('compact',0)->get();
        $num=$reserves->count();
        $limit=10;
        $pageall=ceil($num/$limit);
        //$reserve=Reserve::where('progress','>',3)->forPage($page,$limit)->get();
        $reserve=Reserve::where('progress','=',4)->where('compact_notes',1)->where('compact',0)->forPage($page,$limit)->get();
        foreach($reserve as $k=>$v){
            $user_id=$v->user_id;
            $user=User::find($user_id);
            $staff=Staff::find($v->staff_id);
            if($staff){
                $staffname=$staff->name;
                $staffmobile=$staff->mobile;
            }else{
                $staffname='此顾问不存在';
                $staffmobile='此顾问电话不存在';
            }
            $project=Project::find($v->project_id);
            if(!$project){
                response::jsonEncode(401,'error',['status'=>'关联项目失败']);
            }
            $data[$k]=[
                'reserve_id'            =>$v->id,
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date' => $v->date,
                'reserve_stime'  => $v->reserve_stime,
                'reserve_etime'  => $v->reserve_etime,
                'project'        =>$project->name,
                'lou_fen'        =>$v->lou_fen,
                'lou_hao'        =>$v->lou_hao,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'progress'       =>$v->progress,
                'compact'       =>$v->compact,
                'staff_name' => $staffname,
                'staff_mobile' => $staffmobile,
            ];

        }
        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有已完成预约的信息']);
        }

    }



//查询预约时段接口

    public function reserveRestartSel(Request $req){
        $id=$req->id;
        $timeslot = DB::table('timeslots')->get();
        $data=[
            'reserve_id'=> $id,
            'timeslot' => $timeslot,
        ];
        response::jsonEncode(200,'success',$data);
    }
//重新预约
    public function reserveRestart(Request $req){
        $ajaxdata['id']=$req->id;
        DB::table('reserves_vs_timeslots')->where('reserve_id', '=', $req->id)->delete();
        $reserve=Reserve::find($ajaxdata['id']);
        if(!$reserve){
            response::jsonEncode(301,'error',['status'=>'没有预约信息']);
        }
        $reserve->date=$req->date;
        $shijian_xianzhi = date('Y-m-d', strtotime('+1 day'));
        $timestap = strtotime($req->date);
        $req_date = date('Y-m-d', $timestap);
        //response::jsonEncode(412,'error',['status'=>$req->date]);
        if($req_date < $shijian_xianzhi ){
            response::jsonEncode(412,'error',['status'=>'当日不能发起预约,请从明天开始选择!']);
        }
        //调用预约时段下拉框
        $timeslot = DB::table('timeslots')->where('id',$req->timeslot_id)->first();
        if($timeslot){
            $reserve->reserve_stime=$timeslot->started;
            $reserve->reserve_etime=$timeslot->ended;
        }else{
            response::jsonEncode(400,'error',['status'=>'没有获取到预约时段']);
        }
        $reserve->progress=1;
        $reserve->status=3;
        if($req->notes!=$reserve->notes){
            $reserve->notes=$req->notes;//
        }
        if($reserve->save()){

                DB::table('reserves_vs_timeslots')->where('reserve_id', $reserve->id)->delete();
                $arr['created'] = time();
                $arr = ['timeslot_id' => $req->timeslot_id, 'reserve_id' => $reserve->id, 'date' => $req->date];
                DB::table('reserves_vs_timeslots')->insert($arr);
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1) {
                    $code = 200;
                    $message = 'success';
                    $data = ['status' => '修改预约时段并发送成功'];
                    response::jsonEncode($code, $message, $data);
                }else{
                    $code = 200;
                    $message = 'success';
                    $data = ['status' => '修改预约时段成功'];
                    response::jsonEncode($code, $message, $data);
                }
        }else{
            $code=400;
            $message='false';
            $data=['status'=>'修改预约时段失败'];
            response::jsonEncode($code,$message,$data);
        }
    }
//    public function reserveStart(Request $req){
//        $ajaxdata['id']=$req->id;
//        $reserve=Reserve::find($ajaxdata['id']);
//        if(!$reserve){
//            response::jsonEncode(301,'error',['status'=>'没有预约信息']);
//        }
//        $reserve->progress=3;
//        if($reserve->save()){
//            $code=200;
//            $message='success';
//            $data=['status'=>'开始办理成功'];
//            response::jsonEncode($code,$message,$data);
//        }else{
//            $code=400;
//            $message='false';
//            $data=['status'=>'开始办理失败'];
//            response::jsonEncode($code,$message,$data);
//        }
//    }
    public function reserveHaltStart(Request $req){
        $id=$req->id;
        $reserve=Reserve::find($id);
        if(!$reserve){
            response::jsonEncode(301,'error',['status'=>'没有预约信息']);
        }
        $reserve->halt=1;
        if($req->notes!=$reserve->notes){
            $reserve->notes=$req->notes;//
        }
        if($reserve->save()){
            $wx=new WxController();
            $notice=$wx->noticeMB($reserve->id);
            if($notice==1) {
                response::jsonEncode(200, 'success', ['status' => '挂起并推送成功']);
            }else{
                response::jsonEncode(200, 'success', ['status' => '挂起成功']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'挂起失败']);

        }
    }
    public function reserveHaltEnd(Request $req){
        $id=$req->id;
        $reserve=Reserve::find($id);
        if(!$reserve){
            response::jsonEncode(301,'error',['status'=>'没有预约信息']);
        }
        $reserve->halt=2;
        if($req->notes!=$reserve->notes){
            $reserve->notes=$req->notes;//
        }
        if($reserve->save()){
            $wx=new WxController();
            $notice=$wx->noticeMB($reserve->id);
            if($notice==1) {
                response::jsonEncode(200, 'success', ['status' => '挂起恢复并推送成功']);
            }else{
                response::jsonEncode(200, 'success', ['status' => '挂起恢复成功']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'取消挂起失败']);

        }
    }
    //预约完成
    public function reserveEnd(Request $req){
        $ajaxdata['id']=$req->id;
        $staff_id=$req->staff_id;
        $reserve=Reserve::find($ajaxdata['id']);
        $number=Number::where('reserve_id',$reserve->id)->where('number_class',1)->where('number_push',0)->first();
        if(!$reserve){
            response::jsonEncode(301,'error',['status'=>'没有预约信息']);
        }
        $reserve->progress=4;
        $reserve->sign_staff_id=$staff_id;
        $number->end_time=date('Y-m-d H:i:s',time());
        $number->number_status=1;
        if($req->notes!=$reserve->notes){
            $reserve->notes=$req->notes;//
        }
        if($reserve->save() && $number->save()){
            $wx=new WxController();
            $notice=$wx->noticeMB($reserve->id);
            if($notice==1) {
                response::jsonEncode(200, 'success', ['status' => '预约完成并推送成功']);
            }else{
                response::jsonEncode(200, 'success', ['status' => '预约完成']);
            }
        }else{
            $code=400;
            $message='false';
            $data=['status'=>'预约完成失败'];
            response::jsonEncode($code,$message,$data);
        }
    }
//reserveCompactEnd
    public function reserveCompactEnd(Request $req){
        $ids=$req->ids;
        //$ids=[12,13,14,15,19,20];
        $data=[];
        foreach($ids as $k=>$v){
            $reserve=Reserve::find($v);
            $project=Project::find($reserve->project_id);
            $hourse=$project->name.' '.$reserve->lou_fen.'-'.$reserve->lou_hao.','.$reserve->unit.'-'.$reserve->number;
            if(!$reserve){
                $data[$k]=[301,'error',['status'=>"$hourse 没有预约信息"]];
                //response::jsonEncode(301,'error',['status'=>'没有预约信息']);
            }else{
                $reserve->compact=1;
                $reserve->progress=5;
                if($req->notes!=$reserve->notes){
                    $reserve->notes=$req->notes;//
                }
                if($reserve->save()){
                    $wx=new WxController();
                    $notice=$wx->noticeMB($reserve->id);
                    if($notice==1) {
                        $code = 200;
                        $message = 'success';
                        //$data = ['status' => '取证成功'];
                        $data[$k]=[200,'success',['status'=>"$hourse 取证通知推送成功"]];
                        //response::jsonEncode($code, $message, $data);
                    }else{
                        $data[$k]=[200,'success',['status'=>"$hourse 取证成功,但该用户没有绑定微信"]];
//                    response::jsonEncode(200, 'success', ['status' => '发送成功']);
                    }
                }else{
//                    $code=400;
//                    $message='false';
//                    $data=['status'=>'取证失败'];
                    $data[$k]=[400,'error',['status'=>"$hourse 取证失败"]];
                    //response::jsonEncode($code,$message,$data);
                }
            }
        }
        response::jsonEncode(200,'success',$data);
    }
//预约查询
    public function reserveSelect(Request $req){
        $id=$req->id;
        $reserve=Reserve::find($id);
        $time=date('H',time());
//        if($time >= 16){
//            response::jsonEncode(402,'error',['status'=>'当前时间不能修改预约']);
//        }
        if(!$reserve){
            response::jsonEncode(400,'error',['status'=>'查找失败']);
        }
        $user=User::find($reserve->user_id);
        $project=Project::find($reserve->project_id);
        $banks=Bank::where('project_id',$project->id)->get();
        if($banks){
            $bank=[];
            foreach($banks as $k=>$v){
                $bank[]=$v->bank;
            }
            $banklist = RBank::whereIn('bank_id',$bank)->get();
        }
        $timeslot = DB::table('timeslots')->get();
        $bankName = $reserve->isuser_bank_name == 1?$reserve->bank_name:Bank::returnBank($reserve->pay_bank);

        $jUsers = array();
        if($user['ismain'] == 1 && $user['class'] == 3){
            $jUsers = User::where('main_user_id',$user['id'])->get();
        }

        $data=[
            'reserve_id'   =>$reserve->id,
            'user_id'   =>$user['id'],
            'username'  =>$user['username'],
            'user_class' => $user['class'],
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
            'bank_name'  =>$bankName,
            'date'  =>$reserve->date,
            'reserve_stime' =>$reserve->reserve_stime,
            'reserve_etime' =>$reserve->reserve_etime,
            'discount'  =>$reserve->discount,
            'pay_status'   =>$reserve->pay_status,
            'sign_zip'  =>$reserve->sign_zip,
            'reserve_class'=>$reserve->reserve_class,
            'reserve_class_notes'=>$reserve->reserve_class_notes,
            'status'    =>$reserve->status,
            'progress'    =>$reserve->progress,
            'halt'      =>$reserve->halt,
            'special'   =>$reserve->special,
            'notes'     =>$reserve->notes,
            'jUsers' => $jUsers
        ];

        if($data){
            $date=[
                'banks' => $banklist,
                'timeslot'=>$timeslot,
                'data'=>$data,
            ];
            $code=200;
            $message='success';
            response::jsonEncode($code,$message,$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'查找失败']);

        }
    }

    public function reserveEdit(Request $req){
        $time=date('H',time());


        $time=date('HH:mm:ss',time());
        //查询预约限制时间
        $rTime = RTime::where('time_id',env('TIME_ID'))->first();
        if(!$rTime){
            response::jsonEncode(402,'error',['status'=>'修改失败']);
        }
        if($time >= $rTime->time && $req->reserve_center != 1){
            response::jsonEncode(402,'error',['status'=>'当前时间不能发起预约']);
        }

        $reserve=Reserve::find($req->id);
        if(!empty($req->payfor) && $req->total_money && isset($req->discount)&& isset($req->pay_status)&& isset($req->sign_zip)&& isset($req->reserve_class)){
            $reserve->payfor=$req->payfor;
            if($req->payfor == 1){
                $reserve->total_money = $req->total_money;
                $reserve->first_money = 0;
                $reserve->loan_money = 0;
                $reserve->pay_bank=0;
            }elseif($req->payfor ==2 || $req->payfor==3|| $req->payfor==4 ){
                if($req->first_money&& $req->loan_money && !empty($req->bank_name)){
                    $reserve->total_money = $req->total_money;
                    $reserve->first_money = $req->first_money;
                    $reserve->loan_money = $req->loan_money;
                    $reserve->pay_bank=0;
                    $reserve->bank_name = $req->bank_name;
                    $reserve->isuser_bank_name = 1;
                }else{
                    response::jsonEncode(401,'error',['status'=>'付款金额以及银行信息不完整']);

                }
            }else{
                response::jsonEncode(400,'error',['status'=>'付款方式选择错误']);
            }
            $reserve->discount=$req->discount;//
            $reserve->pay_status=$req->pay_status;//
            $reserve->sign_zip=$req->sign_zip;//

            $reserve->reserve_class=$req->reserve_class;//
            if($req->reserve_class==1 || $req->reserve_class==2){
                $reserve->reserve_class_notes='';
            }elseif($req->reserve_class==3){
                if(empty($req->reserve_class_notes)){
                    response::jsonEncode(401,'error',['status'=>'请填写预约类型']);
                }
                if($req->reserve_class_notes!=$reserve->reserve_class_notes){

                    $reserve->reserve_class_notes=$req->reserve_class_notes;//
                }
            }

    //        $reserve->status=1;
    //        $reserve->progress=1;
            $reserve->special=$req->special;//

            if($req->notes!=$reserve->notes){
                $reserve->notes=$req->notes;//
            }
            if($reserve->save()){
                $code=200;
                $message='success';
                $data=['status'=>'修改成功'];
                response::jsonEncode($code,$message,$data);
            }else{
                $code=400;
                $message='false';
                $data=['status'=>'修改失败'];
                response::jsonEncode($code,$message,$data);
            }
        }else{
            response::jsonEncode(401,'error',['status'=>'预约信息不完整']);
        }
    }

    //超级管理员修改预约
    public function reserveAdminEdit(Request $req){
        $reserve=Reserve::find($req->id);
        $data = [
           'lou_fen' => $req->lou_fen,
           'lou_hao' => $req->lou_hao,
           'unit' => $req->unit,
           'number' => $req->number,
           'total_money' => $req->total_money,
           'pay_status' => $req->pay_status,
           'sign_zip' => $req->sign_zip,
           'reserve_class' => $req->reserve_class,
        ];
        $validator = Validator::make($data, [
            'lou_fen' => 'required',
            'lou_hao' => 'required',
            'unit' => 'required',
            'number' => 'required',
            'total_money' => 'required',
            'pay_status' => 'required',
            'sign_zip' => 'required',
            'reserve_class' => 'required',
        ]);
        if($validator->fails()){
            response::jsonEncode(401,'error',['status'=>'预约信息不完整']);
        }

            if(trim($req->lou_fen)==$reserve->lou_fen && trim($req->lou_hao)==$reserve->lou_hao && trim($req->unit)==$reserve->unit && trim($req->number)==$reserve->number) {
                $reserve->lou_fen = trim($req->lou_fen);
                $reserve->lou_hao = trim($req->lou_hao);
                $reserve->unit = trim($req->unit);
                $reserve->number = trim($req->number);
                
            } else {
                $reserves=Reserve::where('project_id',$reserve->project_id)->where('lou_fen',$req->lou_fen)->where('lou_hao',$req->lou_hao)->where('unit',$req->unit)->where('number',$req->number)->first();
                if($reserves){
                    response::jsonEncode(400,'error',['status'=>'房源不能或已被预约']);
                }else{
                    $reserve->lou_fen=trim($req->lou_fen);
                    $reserve->lou_hao=trim($req->lou_hao);
                    $reserve->unit=trim($req->unit);
                    $reserve->number=trim($req->number);
                    
                }
            }
            $reserve->payfor=$req->payfor;
            if($req->payfor == 1){
                $reserve->total_money = $req->total_money;
                $reserve->first_money = 0;
                $reserve->loan_money = 0;
                $reserve->pay_bank=0;
            }elseif($req->payfor ==2 || $req->payfor==3|| $req->payfor==4 ){
                if($req->first_money&&$req->loan_money && !empty($req->pay_bank)){
                    $reserve->total_money = $req->total_money;
                    $reserve->first_money = $req->first_money;
                    $reserve->loan_money = $req->loan_money;
                    $reserve->pay_bank=$req->pay_bank;
                }else{
                    response::jsonEncode(401,'error',['status'=>'付款金额以及银行信息不完整']);
    
                }
    
            }else{
                response::jsonEncode(400,'error',['status'=>'付款方式选择错误']);
            }
            $reserve->discount=$req->discount;//
            $reserve->pay_status=$req->pay_status;//
            $reserve->sign_zip=$req->sign_zip;//
    
            $reserve->reserve_class=$req->reserve_class;//
            if($req->reserve_class==1 || $req->reserve_class==2){
                $reserve->reserve_class_notes='';
            }elseif($req->reserve_class==3){
                if(empty($req->reserve_class_notes)){
                    response::jsonEncode(401,'error',['status'=>'请填写预约类型']);
                }
                if($req->reserve_class_notes!=$reserve->reserve_class_notes){
    
                    $reserve->reserve_class_notes=$req->reserve_class_notes;//
                }
            }
            $reserve->special=$req->special;//
    
            if($req->notes!=$reserve->notes){
                $reserve->notes=$req->notes;//
            }
            if($reserve->save()){
                $code=200;
                $message='success';
                $data=['status'=>'修改成功'];
                response::jsonEncode($code,$message,$data);
            }else{
                $code=400;
                $message='false';
                $data=['status'=>'修改失败'];
                response::jsonEncode($code,$message,$data);
            }
    }
    //取消预约
    public function reserveRemove(Request $req){
        $id=$req->id;
        DB::table('reserves_vs_timeslots')->where('reserve_id', '=', $id)->delete();
        $reserve=Reserve::find($id);
        $reserve->status=2;
        $reserve->progress=0;
        if($req->notes!=$reserve->notes){
            $reserve->notes=$req->notes;//
        }
        if($reserve->save()){
            $wx=new WxController();
            $notice=$wx->noticeMB($id);
            if($notice==1){
                response::jsonEncode(200,'success',['status'=>'取消预约并推送成功']);
            }else{
                response::jsonEncode(200,'success',['status'=>'取消预约成功']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'取消预约失败']);

        }
    }



//    public function reserveSelectTime(Request $req){
//        $order=$req->order;
//        //dd($order);
//        $reserves=Reserve::orderBy('reserve_time',$order)->orderBy('id','desc')->get();
//        //dd($reserves);
//        foreach($reserves as $k=>$v){
//            $user_id=$v->user_id;
//            $user=User::find($user_id);
//            $data[$k]=[
//                'username'      =>$user['username'],
//                'mobile'        =>$user['mobile'],
//                'reserve_time'  =>$v->reserve_time,
//                'houses'        =>$v->houses,
//                'unit'          =>$v->unit,
//                'number'        =>$v->number,
//                'status'        =>$v->status,
//                'progress'        =>$v->progress,
//            ];
//        }
//        response::jsonEncode(200,'success',$data);
//    }
    public function reserveSelectStatus(Request $req){

        $status=$req->status;
        $reserves=Reserve::where('status',$status)->get();
        foreach($reserves as $k => $v) {
            $user_id=$v->user_id;
            $project=Project::find($v->project_id);
            $user=User::find($user_id);
            $data[$k]=[
                'reserve_id'      =>$v['id'],
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date'  =>$v->date,
                'reserve_stime'  =>$v->reserve_stime,
                'reserve_etime'  =>$v->reserve_etime,
                'project'        =>$project->name,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'        =>$v->progress,
            ];

        }
        if(empty($data)){
            response::jsonEncode(400,'success',['status'=>'当前预约状态没信息']);
        }
        response::jsonEncode(200,'success',$data);

    }

    public function reserveSelectProgress(Request $req){
        $progress=$req->progress;
        $reserves=Reserve::where('progress',$progress)->orderBy('id','desc')->get();
        //dd($reserves);
        foreach($reserves as $k=>$v){
            $user_id=$v->user_id;
            $project=Project::find($v->project_id);
            $user=User::find($user_id);
            $data[$k]=[
                'reserve_id'      =>$v['id'],
                'username'      =>$user['username'],
                'mobile'        =>$user['mobile'],
                'date'  =>$v->date,
                'reserve_stime'  =>$v->reserve_stime,
                'reserve_etime'  =>$v->reserve_etime,
                'project'        =>$project->name,
                'unit'          =>$v->unit,
                'number'        =>$v->number,
                'status'        =>$v->status,
                'progress'        =>$v->progress,
            ];

        }
        if(empty($data)){
            response::jsonEncode(400,'success',['status'=>'当前预约状态没信息']);
        }
        response::jsonEncode(200,'success',$data);

    }


    public function reserveSelectAll(Request $req, $key, $progress=1){
//        $key=$req->key;
//        $key=trim($key);

        if(is_numeric($key)){
            $findusers=User::where('mobile',$key)->get();
            $users = array();
            foreach ($findusers as $u){
                if($u->class == 3 && $u->ismain == 0){
                    $fU = User::where('id',$u->main_user_id)->first();
                    if($fU)$users[] = $fU;
                }else{
                    $users[] = $u;
                }
            }

            $data=array();
            foreach($users as $k=>$v){
                $data[]=$v->id;
            }
            if(empty($data)){
                response::jsonEncode(301,'error',['status'=>'该号码没有用户信息']);
            }

            if($progress == 4){
                //$reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->get();
                $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->where('compact_notes','=',0)->get();

            }else if($progress == 5){
                //$reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->get();
                $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->where('compact_notes','=',1)->get();

            }else{
//                $reserves=Reserve::whereIn('user_id',$data)->where('progress','<',4)->get();
                $reserves=Reserve::whereIn('user_id',$data)->get();
            }
            $data=[];
            foreach($reserves as $k=>$v){
                $project=Project::find($v->project_id);
                $user=User::find($v->user_id);
                $staff=Staff::find($v->staff_id);
                if($staff){
                    $staffname=$staff->name;
                    $staffmobile=$staff->mobile;
                }else{
                    $staffname='此顾问不存在';
                    $staffmobile='此顾问电话不存在';
                }
                $data[]=[
                    'reserve_id'      =>$v['id'],
                    'username'      =>$user['username'],
                    'mobile'        =>$user['mobile'],
                    'date'  =>$v->date,
                    'reserve_stime'  =>$v->reserve_stime,
                    'reserve_etime'  =>$v->reserve_etime,
                    'project'        =>$project->name,
                    'lou_fen'        =>$v->lou_fen,
                    'lou_hao'        =>$v->lou_hao,
                    'unit'          =>$v->unit,
                    'number'        =>$v->number,
                    'status'        =>$v->status,
                    'progress'        =>$v->progress,
                    'staff_name' => $staffname,
                    'staff_mobile' => $staffmobile,
                ];
            }
            if(empty($data)){
                response::jsonEncode(302,'error',['status'=>'当前预约进度下该用户没有信息']);
            }
            response::jsonEncode(200,'success',$data);
        }else{
            $findusers=User::where('username',$key)->get();
            $users = array();
            foreach ($findusers as $u){
                if($u->class == 3 && $u->ismain == 0){
                    $fU = User::where('id',$u->main_user_id)->first();
                    if($fU)$users[] = $fU;
                }else{
                    $users[] = $u;
                }
            }
            //dd($users);
            $data=array();
            foreach($users as $k=>$v){
                $data[]=$v->id;
            }
            if(empty($data)){
//                response::jsonEncode(301,'error',['status'=>'该姓名没有用户信息']);
                //dd($data);
                $projects=Project::where('name',$key)->get();
                $data=array();
                foreach($projects as $k=>$v){
                    $data[]=$v->id;
                }
                if(empty($data)){
                    response::jsonEncode(301,'error',['status'=>'该房源没有用户信息']);
                }else {

                    if($progress==4){
//                        $reserves=Reserve::whereIn('project_id',$data)->where('progress','=',4)->get();
                        $reserves=Reserve::whereIn('project_id',$data)->where('progress','=',4)->where('compact_notes','=',0)->get();
                    }else if($progress == 5){
                        //$reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->get();
                        $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->where('compact_notes','=',1)->get();

                    }else{

//                        $reserves=Reserve::whereIn('project_id',$data)->where('progress','=',1)->get();
                        $reserves=Reserve::whereIn('project_id',$data)->get();
                    }
                    //$reserves = Reserve::whereIn('project_id', $data)->get();
                    $data=[];
                    foreach($reserves as $k=>$v){
                        $project=Project::find($v->project_id);
                        $user=User::find($v->user_id);
                        $staff=Staff::find($v->staff_id);
                        if($staff){
                            $staffname=$staff->name;
                            $staffmobile=$staff->mobile;
                        }else{
                            $staffname='此顾问不存在';
                            $staffmobile='此顾问电话不存在';
                        }
                        $data[]=[
                            'reserve_id'      =>$v['id'],
                            'username'      =>$user['username'],
                            'mobile'        =>$user['mobile'],
                            'date'  =>$v->date,
                            'reserve_stime'  =>$v->reserve_stime,
                            'reserve_etime'  =>$v->reserve_etime,
                            'project'        =>$project->name,
                            'lou_fen'        =>$v->lou_fen,
                            'lou_hao'        =>$v->lou_hao,
                            'unit'          =>$v->unit,
                            'number'        =>$v->number,
                            'status'        =>$v->status,
                            'progress'        =>$v->progress,
                            'staff_name' => $staffname,
                            'staff_mobile' => $staffmobile,
                        ];

                    }
                    if(empty($data)){
                        response::jsonEncode(302,'error',['status'=>'当前房源没有预约信息']);
                    }
                    response::jsonEncode(200,'success',$data);
                }
            }else{
                if($progress==4){
//                    $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->get();
                    $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->where('compact_notes','=',0)->get();
                }else if($progress == 5){
                    //$reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->get();
                    $reserves=Reserve::whereIn('user_id',$data)->where('progress','=',4)->where('compact_notes','=',1)->get();

                }else{
//                    $reserves=Reserve::whereIn('user_id',$data)->where('progress', '=', 1)->get();
                    $reserves=Reserve::whereIn('user_id',$data)->get();
                }

                $data=[];
                foreach($reserves as $k=>$v){
                    $project=Project::find($v->project_id);
                    $user=User::find($v->user_id);
                    $staff=Staff::find($v->staff_id);
                    if($staff){
                        $staffname=$staff->name;
                        $staffmobile=$staff->mobile;
                    }else{
                        $staffname='此顾问不存在';
                        $staffmobile='此顾问电话不存在';
                    }
                    $data[]=[
                        'reserve_id'      =>$v['id'],
                        'username'      =>$user['username'],
                        'mobile'        =>$user['mobile'],
                        'date'  =>$v->date,
                        'reserve_stime'  =>$v->reserve_stime,
                        'reserve_etime'  =>$v->reserve_etime,
                        'project'        =>$project->name,
                        'lou_fen'        =>$v->lou_fen,
                        'lou_hao'        =>$v->lou_hao,
                        'unit'          =>$v->unit,
                        'number'        =>$v->number,
                        'status'        =>$v->status,
                        'progress'        =>$v->progress,
                        'staff_name' => $staffname,
                        'staff_mobile' => $staffmobile,
                    ];

                }
                if(empty($data)){
                    response::jsonEncode(305,'error',['status'=>'当前预约进度下该用户没有信息']);
                }
                response::jsonEncode(200,'success',$data);
            }

            }
        }

    public function reserveselNoCom(Request $req, $key){

            $findusers=User::where('mobile',$key)->orWhere('username',$key)->get();
            $users = array();
            foreach ($findusers as $u){
                if($u->class == 3 && $u->ismain == 0){
                    $fU = User::where('id',$u->main_user_id)->first();
                    if($fU)$users[] = $fU;
                }else{
                    $users[] = $u;
                }
            }

            $data=array();
            foreach($users as $k=>$v){
                $data[]=$v->id;
            }
            if(empty($data)){
                response::jsonEncode(301,'error',['status'=>'该号码没有用户信息']);
            }

            $reserves=Reserve::whereIn('user_id',$data)->where('progress','<',4)->where('status','!=',4)->orderBy('date', 'desc')->where('compact_notes','=',0)->get();

            $data=[];
            foreach($reserves as $k=>$v){
                $project=Project::find($v->project_id);
                $user=User::find($v->user_id);
                $staff=Staff::find($v->staff_id);
                if($staff){
                    $staffname=$staff->name;
                    $staffmobile=$staff->mobile;
                }else{
                    $staffname='此顾问不存在';
                    $staffmobile='此顾问电话不存在';
                }
                $data[]=[
                    'reserve_id'      =>$v['id'],
                    'username'      =>$user['username'],
                    'mobile'        =>$user['mobile'],
                    'date'  =>$v->date,
                    'reserve_stime'  =>$v->reserve_stime,
                    'reserve_etime'  =>$v->reserve_etime,
                    'project'        =>$project->name,
                    'lou_fen'        =>$v->lou_fen,
                    'lou_hao'        =>$v->lou_hao,
                    'unit'          =>$v->unit,
                    'number'        =>$v->number,
                    'status'        =>$v->status,
                    'progress'        =>$v->progress,
                    'staff_name' => $staffname,
                    'staff_mobile' => $staffmobile,
                ];
            }
            if(empty($data)){
                response::jsonEncode(302,'error',['status'=>'当前预约进度下该用户没有信息']);
            }else{
                response::jsonEncode(200,'success',$data);
            }
    }

    public function reserveProjectUpdateStatus(Request $req){
        $ids = $req->input('ids');
        $status =  Reserve::whereIn('id',$ids)->update(['progress'=>4]);
        if($status){
            response::jsonEncode(200,'success','');
        }else{
            response::jsonEncode(201,'success','预约进度修改失败');
        }
    }

    //叫号机接口
    //输入用户返回预约信息
    //判断用户电话号+身份证后四位  或者 身份证全部
    public function getReserve($mobile=0,$card_four=0,$card=0)
    {
        $date=date('H:i',time());
        if (($mobile && $card_four) || $card) {
            if($mobile && $card_four){
                $user = User::where(['mobile'=>$mobile,'class'=>1])->first();
                if(!$user){
                    $data = ['status' => '请输入正确的验证信息或查无此人'];
                    response::jsonEncode('202', 'error', $data);
                }
                $four_card = substr($user->card_id, -4);
                if($four_card == $card_four){
                    $reserve = Reserve::where('user_id', $user->id)->whereIn('progress',[1,3])->get();
                    $data = array();
                    foreach ($reserve as $k => $v) {
                        $star_time = $v->reserve_stime;
                        $end_time = $v->reserve_etime;
                        if($v->date==date('Y-m-d')) {
                            $data[$k]['id'] = $v->id;
                        }
                    }

                    if (!empty($data)) {
                        foreach ($data as $k => $v) {

                            $reserve = Reserve::where('id', $v['id'])->whereIn('progress',[1,3])->first();
                            $reserve_etime=$reserve->reserve_etime;
                            $reserve_stime=$reserve->reserve_stime;
                            $project=Project::find($reserve->project_id);
                            switch($reserve->payfor){
                                case 1:
                                    $payfor = "一次性付款";
                                    $payfor_flag=true;
                                    break;
                                case 2:
                                    $payfor = "银行按揭";
                                    $payfor_flag=false;
                                    break;
                                case 3:
                                    $payfor = "公积金";
                                    $payfor_flag=false;
                                    break;
                                case 4:
                                    $payfor = "组合贷";
                                    $payfor_flag=false;
                                    break;
                                default:
                                    $payfor='系统错误';
                                    $payfor_flag=false;
                            }
                            if($reserve->pay_bank!= 0){
                                $bank_name=xxtemp::banks($reserve->pay_bank);
                            }else{
                                $bank_name='无';
                            }
                            $data[$k] = [
                                'id' => $reserve->id,
                                'username' => $user->username,
                                'mobile' => $user->mobile,
                                'project' => $project->name,
                                'lou_fen'        =>$reserve->lou_fen,
                                'lou_hao'        =>$reserve->lou_hao,
                                'unit' => $reserve->unit,
                                'number' => $reserve->number,
                                'payfor' => $reserve->payfor,
                                'payfor_flag' => $payfor_flag,
                                'pay_bank' => $bank_name,
                                'date' => $reserve->date,
                                'reserve_stime' => $reserve_stime,
                                'reserve_etime' => $reserve_etime,
                                'discount' => $reserve->discount,
                                'pay_status' => $reserve->pay_status,
                                'sign_zip' => $reserve->sign_zip,
                                'reserve_class' => $reserve->reserve_class,
                                'reserve_class_notes' => $reserve->reserve_class_notes,
                                //'status'            => $reserve->status,
                                'progress' => $reserve->progress,
                                //'halt'              => $reserve->halt,
                                //'num'               => $reserve->num,
                                //'number_class'      => $reserve->number_class,
                                //'number_status'     => $reserve->number_status,
                                'special' => $reserve->special,
                                'notes' => $reserve->notes,
                            ];
                        }
                        response::jsonEncode('200','success',$data);
//                        if($reserve->save()){

//                            response::jsonEncode('200','success',$data);
//                        }
                    }else {
                        $data = ['status' => '您当前日期没有预约,请您核对预约信息'];
                        response::jsonEncode('203', 'error', $data);
                    }
                }else{
                    $data = ['status' => '请输入正确的验证信息或查无此人'];
                    response::jsonEncode('202', 'error', $data);
                }
            }
            if($card){
                $user = User::where(['card_id'=>$card,'class'=>1])->first();
                if($user){
                    $reserve = Reserve::where('user_id', $user->id)->whereIn('progress',[1,3])->get();
                    $data = array();
                    foreach ($reserve as $k => $v) {
                        if ($v->date==date('Y-m-d')) {
                            $data[$k]['id'] = $v->id;
                        }
                    }
                    if (count($data) > 0) {
                        foreach ($data as $k => $v) {
                            $reserve = Reserve::where('id', $v['id'])->whereIn('progress',[1,3])->first();
                            $reserve_etime=$reserve->reserve_etime;
                            $reserve_stime=$reserve->reserve_stime;
                            $project=Project::find($reserve->project_id);
                            switch($reserve->payfor){
                                case 1:
                                    $payfor = "一次性付款";
                                    $payfor_flag=true;
                                    break;
                                case 2:
                                    $payfor = "银行按揭";
                                    $payfor_flag=false;
                                    break;
                                case 3:
                                    $payfor = "公积金";
                                    $payfor_flag=false;
                                    break;
                                case 4:
                                    $payfor = "组合贷";
                                    $payfor_flag=false;
                                    break;
                                default:
                                    $payfor='系统错误';
                                    $payfor_flag=false;
                            }
                            if($reserve->pay_bank!= 0){
                                $bank_name=xxtemp::banks($reserve->pay_bank);
                            }else{
                                $bank_name='无';
                            }
                            $data[$k] = [
                                'id' => $reserve->id,
                                'username' => $user->username,
                                'mobile' => $user->mobile,
                                'houses' => $project->name,
                                'lou_fen'  =>$reserve->lou_fen,
                                'lou_hao'  =>$reserve->lou_hao,
                                'unit' => $reserve->unit,
                                'number' => $reserve->number,
                                'payfor' => $reserve->payfor,
                                'payfor_flag' => $payfor_flag,
                                'pay_bank' => $bank_name,
                                'date' => $reserve->date,
                                'reserve_stime' => $reserve_stime,
                                'reserve_etime' => $reserve_etime,
                                'discount' => $reserve->discount,
                                'pay_status' => $reserve->pay_status,
                                'sign_zip' => $reserve->sign_zip,
                                'reserve_class' => $reserve->reserve_class,
                                'reserve_class_notes' => $reserve->reserve_class_notes,
                                //'status'            => $reserve->status,
                                'progress' => $reserve->progress,
                                //'halt'              => $reserve->halt,
                                //'num'               => $reserve->num,
                                //'number_class'      => $reserve->number_class,
                                //'number_status'     => $reserve->number_status,
                                'special' => $reserve->special,
                                'notes' => $reserve->notes,
                            ];

                        }
//                        if($reserve->save()){
//
                    response::jsonEncode('200','success',$data);
//                        }
                    }else {
                        $data = ['status' => '您当前日期没有预约,请您核对预约信息'];
                        response::jsonEncode('203', 'error', $data);
                    }
                }else{
                    $data = ['status' => '请输入正确的验证信息或查无此人'];
                    response::jsonEncode('202', 'error', $data);
                }
            }

        }else{
            $data = ['status' => '路由参数有误'];
            response::jsonEncode('201', 'error', $data);
        }

    }

//输入公司注册号查询当前时间段预约信息
    public function getComReserve(Request $req){
        $card=$req->regnum;
        $date=date('H:i', time());
        $user=User::where(['card_id'=>$card,'class'=>2])->first();
        //dd($user);
        if($user){
            $reserve=Reserve::where('user_id',$user->id)->get();
            $data=array();
            foreach($reserve as $k=>$v){
                if( $v->date== date('Y-m-d')){
                    $data[$k]['id']=$v->id;
                }
            }
            if(!empty($data)){
                foreach($data as $k=>$v){
                    $reserve=Reserve::where('id',$v['id'])->first();
                    $reserve_etime=$reserve->reserve_etime;
                    $reserve_stime=$reserve->reserve_stime;
                    $project=Project::find($reserve->project_id);
                    switch($reserve->payfor){
                        case 1:
                            $payfor = "一次性付款";
                            $payfor_flag=true;
                            break;
                        case 2:
                            $payfor = "银行按揭";
                            $payfor_flag=false;
                            break;
                        case 3:
                            $payfor = "公积金";
                            $payfor_flag=false;
                            break;
                        case 4:
                            $payfor = "组合贷";
                            $payfor_flag=false;
                            break;
                        default:
                            $payfor='系统错误';
                            $payfor_flag=false;
                    }
                    if($reserve->pay_bank!= 0){
                        $bank_name=xxtemp::banks($reserve->pay_bank);
                    }else{
                        $bank_name='无';
                    }
                    $data[$k]=[
                        'id'                => $reserve->id,
                        'username'          => $user->username,
                        'mobile'            => $user->mobile,
                        'project'            => $project->name,
                        'lou_fen'        =>$reserve->lou_fen,
                        'lou_hao'        =>$reserve->lou_hao,
                        'unit'              => $reserve->unit,
                        'number'            => $reserve->number,
                        'payfor'            => $reserve->payfor,
                        'payfor_flag' => $payfor_flag,
                        'pay_bank'          => $bank_name,
                        'reserve_stime'     => $reserve_stime,
                        'reserve_etime'     => $reserve_etime,
                        'discount'          => $reserve->discount,
                        'pay_status'        => $reserve->pay_status,
                        'sign_zip'          => $reserve->sign_zip,
                        'reserve_class'     => $reserve->reserve_class,
                        'reserve_class_notes'     => $reserve->reserve_class_notes,
                        //'status'            => $reserve->status,
                        'progress'          => $reserve->progress,
                        //'halt'              => $reserve->halt,
                        //'num'               => $reserve->num,
                        //'number_class'      => $reserve->number_class,
                        //'number_status'     => $reserve->number_status,
                        'special'           => $reserve->special,
                        'notes'             => $reserve->notes,
                    ];
//                    $reserve->num_class=1;
                }
//                if($reserve->save()){
//
                response::jsonEncode('200','success',$data);
//                }
            }else{
                $data=['status'=>'您当前时间段没有预约,请您核对预约信息'];
                response::jsonEncode('203','error',$data);
            }
        }else{
            $data=['status'=>'请正确输入注册号或查无此人'];
            response::jsonEncode('201','error',$data);
        }
    }

    //验证信息获取个人合同接口
    public function getComPact($mobile=0,$card_four=0,$card=0){
        $date = date('H:i',time());
        if (($mobile && $card_four) || $card) {
            if($mobile && $card_four){
                $user = User::where(['mobile'=>$mobile,'class'=>1])->first();
                //dd($user);
                $four_card = substr($user->card_id, -4);
                if($four_card == $card_four){
                    $reserve = Reserve::where(['user_id'=>$user->id,'progress'=>4])->get();
                    $data = array();
                    foreach ($reserve as $k => $v) {
//                        $star_time = $v->reserve_stime;
//                        $end_time = $v->reserve_etime;
//                        if ($star_time < $date && $date < $end_time) {
                            $data[$k]['id'] = $v->id;
//                        }
                    }

                    if (!empty($data)) {
                        foreach ($data as $k => $v) {
                            $reserve = Reserve::where('id', $v['id'])->first();
                            $reserve_etime=$reserve->reserve_etime;
                            $reserve_stime=$reserve->reserve_stime;
                                $data[$k] = [
                                    'id'    => $reserve->id,
                                    'status'=>'合同信息验证成功',
                                    'username' => $user->username,
                                    'mobile' => $user->mobile,
                                    'project_id' => $reserve->project_id,
                                    'lou_fen'        =>$reserve->lou_fen,
                                    'lou_hao'        =>$reserve->lou_hao,
                                    'unit' => $reserve->unit,
                                    'number' => $reserve->number,
                                    'payfor' => $reserve->payfor,
                                    'pay_bank' => $reserve->pay_bank,
                                    'date' => $reserve->date,
                                    'reserve_stime' => $reserve_stime,
                                    'reserve_etime' => $reserve_etime,
                                    'discount' => $reserve->discount,
                                    'pay_status' => $reserve->pay_status,
                                    'sign_zip' => $reserve->sign_zip,
                                    'reserve_class' => $reserve->reserve_class,
                                    'reserve_class_notes' => $reserve->reserve_class_notes,
                                    //'status'            => $reserve->status,
                                    'progress' => $reserve->progress,
                                    //'halt'              => $reserve->halt,
                                    //'num'               => $reserve->num,
                                    //'number_class'      => $reserve->number_class,
                                    //'number_status'     => $reserve->number_status,
                                    'special' => $reserve->special,
                                    'notes' => $reserve->notes,
                                ];

                            //$reserve->num_class=2;
                        }
//                        if($reserve->save()){
//
                        response::jsonEncode('200','success',$data);
//                        }
                    }
//                    else {
//                        $data = ['status' => '您当前时间段没有预约,请您核对预约信息'];
//                        response::jsonEncode('203', 'error', $data);
//                    }
                }else{
                    $data = ['status' => '请输入正确的验证信息或查无此人'];
                    response::jsonEncode('202', 'error', $data);
                }
            }
            if($card){
                $user = User::where(['card_id'=>$card,'class'=>1])->first();

                if($user){
                    $reserve = Reserve::where(['user_id'=>$user->id,'progress'=>4])->get();
                    $data = array();
                    foreach ($reserve as $k => $v) {
//                        $star_time = $v->reserve_stime;
//                        $end_time = $v->reserve_etime;
//                        if ($star_time < $date && $date < $end_time && $v->date==date('Y-m-d')) {
                            $data[$k]['id'] = $v->id;
//                        }
                    }

                    if (!empty($data)) {
                        foreach ($data as $k => $v) {
                            $reserve = Reserve::where('id', $v['id'])->first();
                            $reserve_etime=date('Y-m-d H:i',$reserve->reserve_etime);
                            $reserve_stime=date('Y-m-d H:i',$reserve->reserve_stime);
                            $data[$k] = [
                                'id'    => $reserve->id,
                                'status'=>'合同信息验证成功',
                                'username' => $user->username,
                                'mobile' => $user->mobile,
                                'project_id' => $reserve->project_id,
                                'lou_fen'        =>$reserve->lou_fen,
                                'lou_hao'        =>$reserve->lou_hao,
                                'unit' => $reserve->unit,
                                'number' => $reserve->number,
                                'payfor' => $reserve->payfor,
                                'pay_bank' => $reserve->pay_bank,
                                'date' => $reserve->date,
                                'reserve_stime' => $reserve_stime,
                                'reserve_etime' => $reserve_etime,
                                'discount' => $reserve->discount,
                                'pay_status' => $reserve->pay_status,
                                'sign_zip' => $reserve->sign_zip,
                                'reserve_class' => $reserve->reserve_class,
                                'reserve_class_notes' => $reserve->reserve_class_notes,
                                //'status'            => $reserve->status,
                                'progress' => $reserve->progress,
                                //'halt'              => $reserve->halt,
                                //'num'               => $reserve->num,
                                //'number_class'      => $reserve->number_class,
                                //'number_status'     => $reserve->number_status,
                                'special' => $reserve->special,
                                'notes' => $reserve->notes,
                            ];
//                            $reserve->num_class=2;
                        }
//                        if($reserve->save()){

                            response::jsonEncode('200','success',$data);
                        }else{
                        $data = ['status' => '您当前没有合同可以领取'];
                        response::jsonEncode('203', 'error', $data);
                    }
                }else{
                    $data = ['status' => '请输入正确的验证信息或查无此人'];
                    response::jsonEncode('202', 'error', $data);
                }
            }

        }else{
            $data = ['status' => '路由参数有误'];
            response::jsonEncode('201', 'error', $data);
        }
    }



    public function getBusComPact(Request $req){
        $card=$req->regnum;
        $date=date('H:i',time());
        $user=User::where(['card_id'=>$card,'class'=>2])->first();
        if($user){
            $reserve=Reserve::where(['user_id'=>$user->id,'progress'=>4])->get();
            $data=array();
            foreach($reserve as $k=>$v){
                $star_time=$v->reserve_stime;
                $end_time=$v->reserve_etime;
//                if($star_time<$date && $date<$end_time){
                    $data[$k]['id']=$v->id;
//                }
            }
            if(count($data)>0){
                foreach($data as $k=>$v){
                    $reserve=Reserve::where('id',$v['id'])->first();
                    $data[$k]=[
                        'id'                => $reserve->id,
                        'status'            =>'合同信息验证成功',
                    ];
//                    $reserve->num_class=1;
                }
//                if($reserve->save()){
//
                response::jsonEncode('200','success',$data);
//                }
            } else{
                $data=['status'=>'您当前预约未完成,请您完成预约信息'];
                response::jsonEncode('203','error',$data);
            }
        }else{
            $data=['status'=>'请正确输入注册号或查无此人'];
            response::jsonEncode('201','error',$data);
        }
    }



    //绑定叫号到预约信息
    public function getReserveNum(Request $req){
        $id=$req->id;
        //$num_class=$req->num_class;
        $num=$req->num;
        if($id && $num){
            $reserve=Reserve::where('id',$id)->first();
            if($reserve){
                //$reserve->num_class=$num_class;
                if($reserve->progress==1){
                    $reserve->progress=3;
                    $reserve->save();
                    $number=Number::where(['reserve_id'=>$id,'number_class'=>1])->first();
                    if($number){
                        $number->number=$num;
                        $number->time=date('Y-m-d',time());
                        $number->star_time=date('Y-m-d H:i:s',time());
                        $number->number_class=1;
                        $number->number_status=0;
                        $number->number_push=0;
                        if($number->save()){
                            $wx = new WxController();
                            $notice = $wx->noticeMB($reserve->id);
                            if ($notice == 1) {
                                response::jsonEncode(200, 'success', ['status' => '重新预约叫号并推送成功']);
                            } else {
                                response::jsonEncode(200, 'success', ['status' => '重新预约叫号成功']);
                            }
                        }
                        response::jsonEncode('400','error',['status'=>'重新预约叫号绑定失败']);
                    }

                    $number=new Number();
                    $number->reserve_id=$id;
                    $number->number=$num;
                    $number->time=date('Y-m-d',time());
                    $number->star_time=date('Y-m-d H:i:s',time());
                    $number->number_class=1;
                    $number->number_status=0;
                    $number->number_push=0;

                    if($number->save() ){
                        $wx = new WxController();
                        $notice = $wx->noticeMB($reserve->id);
                        if ($notice == 1) {
                            response::jsonEncode(200, 'success', ['status' => '预约叫号并推送成功']);
                        } else {
                            response::jsonEncode(200, 'success', ['status' => '预约叫号成功']);
                        }
                    }else{
                        $data=['status'=>'绑定失败'];
                        response::jsonEncode('400','error',$data);
                    }
                }elseif($reserve->progress==3){
                    //$number=Number::where(['reserve_id'=>$id,'number_class'=>1,])->first();
                    $number=Number::where(['reserve_id'=>$id,'number_class'=>1])->first();
                    if($number){
                        $number->number=$num;
                        $number->time=date('Y-m-d',time());
                        $number->star_time=date('Y-m-d H:i:s',time());
                        $number->number_class=1;
                        $number->number_status=0;
                        $number->number_push=0;
                        if($number->save()){
                            $wx = new WxController();
                            $notice = $wx->noticeMB($reserve->id);
                            if ($notice == 1) {
                                response::jsonEncode(200, 'success', ['status' => '重新预约叫号并推送成功']);
                            } else {
                                response::jsonEncode(200, 'success', ['status' => '重新预约叫号成功']);
                            }
                        }
                        response::jsonEncode('400','error',['status'=>'重新预约叫号绑定失败']);
                    }

                    $number=new Number();
                    $number->reserve_id=$id;
                    $number->number=$num;
                    $number->time=date('Y-m-d',time());
                    $number->star_time=date('Y-m-d H:i:s',time());
                    $number->number_class=1;
                    $number->number_status=0;
                    $number->number_push=0;

                    if($number->save()){
                        $wx = new WxController();
                        $notice = $wx->noticeMB($reserve->id);
                        if ($notice == 1) {
                            response::jsonEncode(200, 'success', ['status' => '重新预约叫号并推送成功']);
                        } else {
                            response::jsonEncode(200, 'success', ['status' => '重新预约叫号成功']);
                        }
                    }
                    response::jsonEncode('400','error',['status'=>'重新取号绑定失败']);

                }elseif($reserve->progress==4){
                    $number=Number::where(['reserve_id'=>$id,'number_class'=>2,'number_push'=>0])->first();
                    if($number){
                        $number->number=$num;
                        $number->star_time=date('Y-m-d H:i:s',time());
                        if($number->save()){
                            $wx = new WxController();
                            $notice = $wx->noticeMB($reserve->id);
                            if ($notice == 1) {
                                response::jsonEncode(200, 'success', ['status' => '重新叫号绑定并推送成功']);
                            } else {
                                response::jsonEncode(200, 'success', ['status' => '重新叫号绑定成功']);
                            }
                        }
                        response::jsonEncode('400','error',['status'=>'重新取号绑定失败']);
                    }
                    $number=new Number();
                    $number->reserve_id=$id;
                    $number->number=$num;
                    $number->time=date('Y-m-d',time());
                    $number->star_time=date('Y-m-d H:i:s',time());
                    $number->number_class=2;
                    $number->number_status=0;
                    $number->number_push=0;
                    $number->time=date('Y-m-d',time());
                    if($number->save()){
                        $wx = new WxController();
                        $notice = $wx->noticeMB($reserve->id);
                        if ($notice == 1) {
                            response::jsonEncode(200, 'success', ['status' => '取证叫号并推送成功']);
                        } else {
                            response::jsonEncode(200, 'success', ['status' => '取证叫号成功']);
                        }
                    }else{
                        $data=['status'=>'绑定失败'];
                        response::jsonEncode('400','error',$data);
                    }
                }else {
                    $data = ['status' => '查无此人'];
                    response::jsonEncode('401', 'error', $data);
                }
            }else{
                $data=['status'=>'查无此人'];
                response::jsonEncode('401','error',$data);
            }
        }else{
            $data=['status'=>'路由参数有误'];
            response::jsonEncode('402','error',$data);
        }
    }
    //过号接口
    public function overReserveNum(Request $req){
        $num=$req->number;
        $date=date('Y-m-d',time());
        if($num){
            $number=Number::where(['number'=>$num,'time'=>$date])->first();
            if($number){
                $number->number_push=1;
                $reserve=Reserve::where('id',$number->reserve_id)->first();
                if($reserve->progress==3){
                    if($number->save()) {
                        $reserve->progress = 2;
                        $reserve->status = 2;
                        if ($reserve->save()) {
                            $wx = new WxController();
                            $notice = $wx->noticeMB($reserve->id);
                            if ($notice == 1) {
                                response::jsonEncode(200, 'success', ['status' => '预约过号并推送成功']);
                            } else {
                                response::jsonEncode(200, 'success', ['status' => '预约过号成功']);
                            }
                        }
                    }
                }else{
                    $data=['status'=>'过号失败'];
                    response::jsonEncode('400','error',$data);
                }
            }else{
                $data=['status'=>'查无此人'];
                response::jsonEncode('401','error',$data);
            }
        }else{
            $data=['status'=>'路由参数有误'];
            response::jsonEncode('402','error',$data);
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//    public function reserveSelectUser(Request $req){
//
//        $reserves=Reserve::where()->orderBy('id','desc')->get();
//        //dd($reserves);
//        foreach($reserves as $k=>$v){
//            $user_id=$v->user_id;
//            $user=User::find($user_id);
//            $data[$k]=[
//                'username'      =>$user['username'],
//                'mobile'        =>$user['mobile'],
//                'reserve_time'  =>$v->reserve_time,
//                'houses'        =>$v->houses,
//                'unit'          =>$v->unit,
//                'number'        =>$v->number,
//                'status'        =>$v->status,
//            ];
//        }
//        response::jsonEncode(200,'success',$data);
//    }
//    public function reserveSelectHouses(){
//        $reserves=Reserve::orderBy('id','desc')->get();
//        //dd($reserves);
//        foreach($reserves as $k=>$v){
//            $user_id=$v->user_id;
//            $user=User::find($user_id);
//            $data[$k]=[
//                'username'      =>$user['username'],
//                'mobile'        =>$user['mobile'],
//                'reserve_time'  =>$v->reserve_time,
//                'houses'        =>$v->houses,
//                'unit'          =>$v->unit,
//                'number'        =>$v->number,
//                'status'        =>$v->status,
//            ];
//        }
//        response::jsonEncode(200,'success',$data);
//    }
}
