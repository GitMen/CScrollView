<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//se Illuminate\Http\Response as Responses;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Staff;
use App\Admin;
use Illuminate\Support\Facades\Session;
//use Illuminate\Session;
use response;
class StaffController extends Controller
{
    //管理员登录接口
    public function adminlogin(Request $req){
        //var_dump($_POST);die();
        $adminid = $req->adminid;
        $psd=$req->psd;
        $password=md5(md5($psd));

        $admin=Admin::where(['adminid'=>$adminid,'password'=>$password])->where('role','<',5)->first();
        $staff=Staff::where(['staffid'=>$adminid,'password'=>$password])->where('role','>=',5)->first();
        if($admin){
            $data=[
                'role' =>$admin->role,
                'admin_id'=>$admin->id,
                'username'=>$admin->name
            ];

            response::jsonEncode(200,'success',$data);
        }
        if($staff){
            $staff_login_time=date('H',time());
            $data=[
                'role' =>$staff->role,
                'staff_id'=>$staff->id,
                'username'=>$staff->name
            ];
//            if($data['role']==5 && $staff_login_time>=16){
//                response::jsonEncode(401,'error',['status'=>'您不在登录时间范围内']);
//            }
            response::jsonEncode(200,'success',$data);
        }else{
            $data=[
                'status'=>'登录失败',
            ];
            response::jsonEncode(400,'false',$data);
        }
    }
    //签约专员登录接口
   public function login(Request $req){

       $adminid = $req->adminid;
       $psd=$req->psd;

       $password=md5(md5($psd));
       $qianyue=Admin::where(['adminid'=>$adminid,'password'=>$password])->where('role','=',8)->first();
       if($qianyue){
           $data=[
               'role' =>$qianyue->role,
               'admin_id'=>$qianyue->id,
               'username'=>$qianyue->name
           ];
           response::jsonEncode(200,'success',$data);
       }else{
           $data=[
               'status'=>'登录失败',
           ];
           response::jsonEncode(400,'false',$data);
       }
   }
    public function adminlogout(Request $req){
//        $req->session()->forget('admin_id');
//        $req->session()->forget('username');
//        $req->session()->forget('role');
        //echo time();die;
        session()->flush();
        session()->save();
        $admin_id=$req->session()->get('admin_id');
        $username=$req->session()->get('username');
        if(!$admin_id && !$username){
            $data=['ok'];
            response::jsonEncode(200,'success',$data);
        }

    }
    public function logout(Request $req){
        $req->session()->forget('staff_id');
        $req->session()->forget('username');
        //$req->session()->flush();
        session()->flush();
        session()->save();
        $staff_id=$req->session()->get('staff_id');
        $username=$req->session()->get('username');
        if(!$staff_id && !$username){
            $data=['ok'];
            response::jsonEncode(200,'success',$data);
        }

    }
    public function staffsel(Request $req){
        $id=$req->id;
        if($id){
            $staff=Staff::find($id);
            $admin=Admin::find($id);
            if($staff){
//                $data=[
//                    'userid'=>$staff->id,
//                    'username'=>$staff->name,
//                    'mobile'=>$staff->mobile
//                ];
                response::jsonEncode(200,'success',['status'=>'员工信息存在']);
            }elseif($admin){
//                $data=[
//                    'userid'=>$admin->id,
//                    'username'=>$admin->name,
//                    'mobile'=>$admin->mobile
//                ];
                response::jsonEncode(200,'success',['status'=>'管理员信息存在']);

            }else{
                $data=['status'=>'查无此人'];
                response::jsonEncode(400,'false',$data);
            }
        }
    }
    public function editPsd(Request $req){
            $id=$req->id;
            $oldpsd=md5(md5($req->oldpsd));
            $new1psd=md5(md5($req->new1psd));
            $new2psd=md5(md5($req->new2psd));
            if($req->role == 5 && $req->role == 6 && $req->role == 7 ){
                $staff=Staff::where('id',$id)->where('role','>',4)->first();
                if($staff->password == $oldpsd){
                    if($new1psd==$new2psd){
                        $staff->password=$new2psd;
                        $staff->save();
                        response::jsonEncode(200,'success',['status'=>'帐号密码修改成功']);
                    }else{
                        response::jsonEncode(401,'false',['status'=>'帐号两次密码输入错误']);
                    }
                }else{
                    response::jsonEncode(400,'false',['status'=>'帐号原密码输入错误']);
                }

            }elseif($req->role < 4 || $req->role == 8){
                $admin=Admin::where('id',$id)->whereIn('role',[0,1,2,3,4,8])->first();
                if($admin->password == $oldpsd){
                    if($new1psd==$new2psd){
                        $admin->password=$new2psd;
                        $admin->save();
                        response::jsonEncode(200,'success',['status'=>'帐号密码修改成功']);
                    }else{
                        response::jsonEncode(401,'false',['status'=>'帐号两次密码输入错误']);
                    }
                }else{
                    response::jsonEncode(400,'false',['status'=>'帐号原密码输入错误']);
                }

        }



    }
    public function findPsd(Request $req){
        $code_session=$req->session()->get('code');
        $code=$req->code;
        if($code_session==$code){
            $mobile=$req->mobile;
            $psd=$req->psd;
            $password=md5(md5($psd));
            $staff=Staff::where('mobile',$mobile)->first();
            if($staff){
                $staff->password=$password;
                if($staff->save()){
                    $data=['status'=>'修改成功'];
                    response::jsonEncode(200,'success',$data);
                }else{
                    $data=['status'=>'修改失败'];
                    response::jsonEncode(400,'false',$data);
                }
            }
        }else{
            $data=['status'=>'验证失败'];
            response::jsonEncode(400,'false',$data);
        }

    }

    public function stafffind(Request $req){
        $mobile=$req->mobile;
        if($mobile){
            $staff=Staff::where('mobile',$mobile)->first();
            if($staff){
                $data=[
                    'userid'=>$staff->id,
                    'username'=>$staff->name,
                    'mobile'=>$staff->mobile
                ];
                response::jsonEncode(200,'success',$data);
            }else{
                $data=['status'=>'查无此人'];
                response::jsonEncode(400,'false',$data);
            }
        }
    }

    public function ddSession(Request $req){
        //dd($req->session()->get('username'));
        dd($req->session()->get('admin_id'));
        //dd(session());
    }
}
