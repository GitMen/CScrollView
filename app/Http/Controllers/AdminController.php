<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Admin;
use App\Staff;
use App\Project;
use App\A_project;
use App\User;
use App\Reserve;
use DB;
use response;

use Overtrue\Wechat\Server;
use Overtrue\Wechat\Message;
use Overtrue\Wechat\User as WxUser;
use Overtrue\Wechat\Menu;
use Overtrue\Wechat\MenuItem;
use Overtrue\Wechat\Notice;
class AdminController extends Controller
{
    public function selProject(){
        $projects = Project::all();
        $data=array();
        foreach($projects as $value){
            $data[]=[
                'id'=>$value->id,
                'name'=>$value->name,
            ];
        }
        if($data){
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'error',['当前没有项目']);

        }
    }
    public function adminAdd(Request $req){
        $admin=new Admin();
        $admin->adminid=$req->adminid;
        $admin->name=$req->name;
        $admin->mobile=$req->mobile;

        $admin->role=$req->role;

        $projects=$req->projects;

        //$admin->apartment_id=$req->apartment_id;

        $admin->password=md5(md5($req->password));
        $admin->time=date('Y-m-d',time());
        $admin->status=0;
        $admin->delstatus=0;
        if($admin->save()){
            if($admin->role==2 || $admin->role==3){
                if($projects){
                    foreach($projects as $v){
                        $a_project=new A_project();
                        $a_project->admin_id=$admin->id;
                        $a_project->project_id=$v;
                        $a_project->save();
                    }
                }
            }
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

    public function adminDelete(Request $req){
        $data['id']=$req->id;
        Admin::where('id',$data['id'])->delete();
        $code=200;
        $message='success';
        $data=['status'=>'删除成功'];
        response::jsonEncode($code,$message,$data);

    }

    public function adminSelect(Request $req){
        $ajaxdata['id']=$req->id;
        $admin=Admin::find($ajaxdata['id']);

        //链表查询admin所属项目
        $rs=DB::table('a_projects')->leftJoin('projects','a_projects.project_id','=','projects.id')->where('a_projects.admin_id','=',$req->id)->get();
        foreach($rs as $k => $value){
            $projects[$k]=[
                'id' => $value->project_id,
                'name' => $value->name
            ];
        }
        if(empty($projects)){
            $projects=[];
        }
        if(!empty($admin)){
            $data=[
                'id' => $admin->id,
                'adminid' => $admin->adminid,
                'name' => $admin->name,
                'mobile' => $admin->mobile,
                'role' => $admin->role,
                'projects' => $projects
            ];
            $message='success';
            $code=200;
            response::jsonEncode($code,$message,$data);
        }else{
            response::jsonEncode(400,'error',['status'=>'没有成员信息']);
        }

    }

    public function adminEdit(Request $req){
//        $admin=Admin::where('id','=',6)->delete();
//            dd($admin);
        $ajaxdata['id']=$req->id;
        $admin=Admin::find($ajaxdata['id']);
        $admin->adminid=$req->adminid;
        $admin->name=$req->name;
        if($req->mobile==$admin->mobile){
            $admin->mobile = $admin->mobile;
        }else{
            $admin=Admin::where('mobile',$req->mobile)->first();
            if(empty($admin)){
                $admin->mobile=$req->mobile;
            }else{
                response::jsonEncode(400,'error',['status'=>'手机号被占用']);
            }
        }
        //$admin->mobile=$req->mobile;

        $admin->role=$req->role;

        $projects=$req->projects;

        //$admin->apartment_id=$req->apartment_id;
        $admin->password=md5(md5($req->password));
        $admin->time=date('Y-m-d',time());
        $admin->status=0;
        $admin->delstatus=0;
        if($admin->save()){
            $a_project_rs=A_project::where('admin_id','=',$admin->id)->delete();
            //dd($a_project_rs);
//            if($a_project_rs==1){
                if($admin->role==2 || $admin->role==3){
                    foreach($projects as $v){
                        $a_project=new A_project();
                        $a_project->admin_id=$admin->id;
                        $a_project->project_id=$v;
                        $a_project->save();
                    }
                }
                $code=200;
                $message='success';
                $data=['status'=>'修改成功'];
                response::jsonEncode($code,$message,$data);
//            }else{
//                response::jsonEncode('400','error',['status'=>'修改项目失败']);
//            }

        }else{
            $code=400;
            $message='error';
            $data=['status'=>'修改失败'];
            response::jsonEncode($code,$message,$data);
        }

//        $admin->name=$req->name;
//        $admin->mobile=$req->mobile;
//        $admin->apartment_id=$req->apartment_id;
//        $admin->role=$req->role;
//        if($admin->save()){
//
//            $code=200;
//            $message='success';
//            $data=['status'=>'修改成功'];
//            response::jsonEncode($code,$message,$data);
//        }else{
//            $code=400;
//            $message='false';
//            $data=['status'=>'修改失败'];
//            response::jsonEncode($code,$message,$data);
//        }
    }
    public function adminlist(Request $req,$page){
        $limit=10;
        $adminall=Admin::where('role','!=','0')->orderBy('id','asc')->get();
        $admins=Admin::where('role','!=','0')->forPage($page,$limit)->orderBy('id','asc')->get();
        $num=$adminall->count();
        $pageall=ceil($num/$limit);
        $data=array();
            foreach($admins as $k=>$v){
                $projects=array();
                $rs=DB::table('a_projects')->leftJoin('projects','a_projects.project_id','=','projects.id')->where('a_projects.admin_id','=',$v->id)->get();
                foreach($rs as $kk => $value){
                    $projects[$kk]=[
                        'id' => $value->project_id,
                        'name' => $value->name
                    ];
                }
                $data[$k]=[
                    'id'    =>$v->id,
                    'adminid'    =>$v->adminid,
                    'name'  =>$v->name,
                    'mobile'  =>$v->mobile,
                    'role'  =>$v->role,
                    'projects'=>$projects,

                ];
            }
            if(empty($data)){
                response::jsonEncode(400,'error',['status'=>'当前没有管理员信息']);
            }else{
                $date=[$num,$pageall,$data];
                $message='success';
                $code=200;
                response::jsonEncode($code,$message,$date);
            }

    }
    //检测工号
    public function getAdminid(Request $req){
        $adminid=$req->adminid;
        $admin=Admin::where('adminid',$adminid)->first();
        if(empty($admin)){
            $staff=Staff::where('staffid',$adminid)->first();
            if(empty($staff)){
                response::jsonEncode(200,'success',['status'=>'工号可以使用']);
            }else{
                response::jsonEncode(400,'error',['status'=>'工号被占用']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'工号被占用']);
        }
    }


    //检查员工手机号唯一
    public function getMobileAdmin(Request $req){
        $mobile=$req->mobile;
        $admin=Admin::where('mobile',$mobile)->first();
        if(empty($admin)){
            $staff=Staff::where('mobile',$mobile)->first();
            if(empty($staff)){
                response::jsonEncode(200,'success',['status'=>'手机号可以使用']);
            }else{
                response::jsonEncode(400,'error',['status'=>'手机号被占用']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'手机号被占用']);
        }
    }
    //检查用户手机号唯一
    public function getMobileUser(Request $req){
        $mobile=$req->mobile;
        $user=User::where('mobile',$mobile)->first();
        if(empty($user)){
                response::jsonEncode(200,'success',['status'=>'手机号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'手机号被占用']);
        }
    }


    //超级管理员修改 预约 进度
    public function adminEditReserver( Request $req , $reserve_id , $id){
        $progress = $id;
        $reserve=Reserve::where('id',$reserve_id)->first();
        if(!$reserve){
            response::jsonEncode(302,'error',['status'=>'没有预约信息']);
        }
        if($progress == 1){
            $reserve->status=1;
            $reserve->progress=1;
            $reserve->halt=0;
            $reserve->compact_notes=0;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 2){
            $reserve->status=2;
            $reserve->progress=0;
            $reserve->halt=0;
            $reserve->compact_notes=0;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 3){
            $reserve->status=1;
            $reserve->progress=3;
            $reserve->halt=1;
            $reserve->compact_notes=0;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 4){
            $reserve->status=1;
            $reserve->progress=3;
            $reserve->halt=2;
            $reserve->compact_notes=0;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 5){
            $reserve->status=1;
            $reserve->progress=4;
            $reserve->halt=0;
            $reserve->compact_notes=0;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 6){
            $reserve->status=1;
            $reserve->progress=4;
            $reserve->halt=0;
            $reserve->compact_notes=1;
            $reserve->compact=0;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }else if($progress == 7){
            $reserve->status=1;
            $reserve->progress=5;
            $reserve->halt=0;
            $reserve->compact_notes=1;
            $reserve->compact=1;
            if($reserve->save()){
                $wx=new WxController();
                $notice=$wx->noticeMB($reserve->id);
                if($notice==1){
                    response::jsonEncode(200,'success',['status'=>'修改进度并推送成功']);
                }else{
                    response::jsonEncode(200,'success',['status'=>'修改进度成功']);
                }
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
        }
    }

}
