<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Staff;
use App\A_project;
use App\Project;
use DB;
use response;

class ApiController extends Controller
{
    //===============================================成员API
    public function sysSelProject(Request $req){
        $id=$req->admin_id;
        $rs=DB::table('a_projects')->leftJoin('projects','a_projects.project_id','=','projects.id')->where('a_projects.admin_id','=',$id)->get();
        if($rs){
            foreach($rs as $k => $value){
                $data[$k]=[
                    'project_id' => $value->project_id,
                    'project_name' => $value->name
                ];
            }
            if($data){
                response::jsonEncode(200,'success',$data);
            }else{
                response::jsonEncode(400,'error',['当前没有项目']);
            }
        }else{
            response::jsonEncode(400,'error',['当前没有项目']);
        }

    }

    public function getStaffid(Request $req){
        $staffid=$req->staffid;
        $staffid=Staff::where('staffid',$staffid)->first();
        if(empty($staffid)){
            response::jsonEncode(200,'success',['status'=>'工号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'工号被占用']);
        }
    }



    public function systemAdd(Request $req){
        $admin_id=$req->admin_id;
        $staff = new Staff();
        if($req->project_id){
            $staff->project_id = $req->project_id;
        }else{
            response::jsonEncode(400,'error',['status'=>'请添加关联项目']);
        }
        // 只能添加项目管理员管理的项目
        $staff->pid=$admin_id;
        $staff->staffid = $req->staffid;
        $staff->name = $req->name;
        $staff->mobile = $req->mobile;
        $staff->role=$req->role;
        $staff->password=md5(md5($req->password));
        $staff->time=date('Y-m-d',time());
        $staff->status=0;
        $staff->delstatus=0;
        if($staff->save()){
            $code=200;
            $message='success';
            $data=['status'=>'添加成功'];
            response::jsonEncode($code,$message,$data);
        }else{
            $code=400;
            $message='false';
            $data=['status'=>'添加失败'];
            response::jsonEncode($code,$message,$data);
        }

    }
    public function systemOtherAdd(Request $req){
        $admin_id=$req->admin_id;
        $staff = new Staff();
        $staff->pid=$admin_id;
        $staff->staffid = $req->staffid;
        $staff->name = $req->name;
        $staff->mobile = $req->mobile;
        $staff->role=$req->role;
        $staff->password=md5(md5($req->password));
        $staff->time=date('Y-m-d',time());
        $staff->status=0;
        $staff->delstatus=0;
        if($staff->save()){
            $code=200;
            $message='success';
            $data=['status'=>'添加成功'];
            response::jsonEncode($code,$message,$data);
        }else{
            $code=400;
            $message='false';
            $data=['status'=>'添加失败'];
            response::jsonEncode($code,$message,$data);
        }
    }
    public function systemDel(Request $req){
        $data['id']=$req->id;
        Staff::where('id',$data['id'])->delete();
        $code=200;
        $message='success';
        $data=['status'=>'删除成功'];
        response::jsonEncode($code,$message,$data);
    }

    public function systemEdit(Request $req){
        $ajaxdata['id']=$req->id;
        $staff=Staff::find($ajaxdata['id']);
        $staff->name = $req->name;
        if($req->mobile==$staff->mobile){
            $staff->mobile = $staff->mobile;
        }else{
            $staff=Staff::where('mobile',$req->mobile)->first();
            if(empty($staff)){
                $staff->mobile=$req->mobile;
            }else{
                response::jsonEncode(400,'error',['status'=>'手机号被占用']);
            }
        }
        $staff->password=md5(md5($req->password));
        $staff->time=date('Y-m-d',time());
        $staff->status=0;
        $staff->delstatus=0;
        if($staff->save()){
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

//编辑销售的时候查询
    public function systemSelect(Request $req){
        $ajaxdata['id']=$req->id;
        $staff=Staff::find($ajaxdata['id']);
        $project=Project::find($staff->project_id);
        $project_id=$staff->project_id;
        $a_project=A_project::where('project_id',$project_id)->first();
        $id=$a_project->admin_id;
        $rs=DB::table('a_projects')->leftJoin('projects','a_projects.project_id','=','projects.id')->where('a_projects.admin_id','=',$id)->get();
        if($rs) {
            foreach ($rs as $k => $value) {
                $data[$k] = [
                    'project_id' => $value->project_id,
                    'project_name' => $value->name
                ];
            }
            $date = [
                'id' => $staff->id,
                'staffid' => $staff->staffid,
                'name' => $staff->name,
                'mobile' => $staff->mobile,
                //'apartment_id'  =>$staff->apartment_id,
                'role' => $staff->role,
                //'status'=>$staff->status,
                'project' => [
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ],
                'projects' => $data,
            ];
            if ($date) {
                $message = 'success';
                $code = 200;
                response::jsonEncode($code, $message, $date);
            } else {
                response::jsonEncode(400, 'eroor', ['status' => '查找失败']);
            }
        }

    }
    //编辑其他员工的时候查询
    public function systemOtherSelect(Request $req){
        $ajaxdata['id']=$req->id;
        $staff=Staff::find($ajaxdata['id']);
        $date = [
            'id' => $staff->id,
            'staffid' => $staff->staffid,
            'name' => $staff->name,
            'mobile' => $staff->mobile,
            //'apartment_id'  =>$staff->apartment_id,
            'role' => $staff->role,
            //'status'=>$staff->status,
        ];
            if ($date) {
                $message = 'success';
                $code = 200;
                response::jsonEncode($code, $message, $date);
            } else {
                response::jsonEncode(400, 'eroor', ['status' => '查找失败']);
            }
        }
    //保存其他员工
    public function systemOtherEdit(Request $req){
        $ajaxdata['id']=$req->id;
        $staff=Staff::find($ajaxdata['id']);
        $staff->name = $req->name;
        if($req->mobile==$staff->mobile){
            $staff->mobile = $staff->mobile;
        }else{
            $staff=Staff::where('mobile',$req->mobile)->first();
            if(empty($staff)){
                $staff->mobile=$req->mobile;
            }else{
                response::jsonEncode(400,'error',['status'=>'手机号被占用']);
            }
        }
        $staff->password=md5(md5($req->password));
        $staff->time=date('Y-m-d',time());
        $staff->status=0;
        $staff->delstatus=0;
        if($staff->save()){
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


    //全部成员列表
    public function systemlist(Request $req,$page=1){
        $limit=10;
        $staffall=Staff::all();
        $num=$staffall->count();
        $pageall=ceil($num/$limit);
        $staffs=Staff::forPage($page,$limit)->orderBy('id','desc')->get();
        $data=array();
            foreach($staffs as $k=>$v){
                $projects=Project::find($v->project_id);
                if($projects){
                    $project=[
                        'id' => $projects->id,
                        'name' => $projects->name,
                    ];
                }else{
                    $project=[
                        'id' => '',
                        'name' => '无项目',
                    ];
                }
                $data[$k]=[
                    'id'    =>$v->id,
                    'staffid'    =>$v->staffid,
                    'name'  =>$v->name,
                    'mobile'  =>$v->mobile,
                    'role'  =>$v->role,
                    'project' =>$project
                ];
            }
            if(empty($data)){
                response::jsonEncode(400,'error',['status'=>'当前没有成员信息']);
            }else{
                $date=[$num,$pageall,$data];
                $message='success';
                $code=200;
                response::jsonEncode($code,$message,$date);
            }
    }
    //管理查看自己的成员列表
    public function systemAdminlist(Request $req,$admin_id,$page=1){
        $limit=10;
        $staffall=Staff::where('pid',$req->admin_id)->get();
        //dd($staffall);
        $num=$staffall->count();
        $pageall=ceil($num/$limit);
        $staffalls=Staff::where('pid',$req->admin_id)->forPage($page,$limit)->orderBy('id','desc')->get();
        //dd($staffalls);
        $data=array();
            foreach($staffalls as $k=>$v){
                $projects=Project::find($v->project_id);
                if($projects){
                    $project=[
                        'id' => $projects->id,
                        'name' => $projects->name,
                    ];
                }else{
                    $project=[
                        'id' => '',
                        'name' => '无项目',
                    ];
                }

                $data[$k]=[
                    'id'    =>$v->id,
                    'staffid'    =>$v->staffid,
                    'name'  =>$v->name,
                    'mobile'  =>$v->mobile,
                    'role'  =>$v->role,
                    'project' =>$project,
                ];
            }
            if(empty($data)){
                response::jsonEncode(400,'error',['status'=>'当前没有成员信息']);
            }else{
                $date=[$num,$pageall,$data];
                $message='success';
                $code=200;
                response::jsonEncode($code,$message,$date);
            }

    }
    //冻结普通成员
    public function staFrozen(Request $req){
        $ajaxdata['id']=$req->id;
        $staff=Staff::find($ajaxdata['id']);
        $staff->status=1;
        if($staff->save()){
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
}
