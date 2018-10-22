<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Project;
use App\RBank;
use App\RTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use response;
use App\User;
use App\Staff;
use App\Admin;
use DB;
use App\Reserve;
class UserController extends Controller
{
    //检查用户身份证号唯一
    public function getCardUser(Request $req){
        $card_id=trim($req->card_id);
        $user=User::where('card_id',$card_id)->where('class',1)->first();
        if(empty($user)){
            response::jsonEncode(200,'success',['status'=>'身份证号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'身份证号被占用']);
        }
    }

    //检查用户身份证号唯一
    public function getCardUsers(Request $req){
        $card_ids=$req->input('card_ids');
        $users=User::whereIn('card_id',$card_ids)->get();
        $cards = [];
        foreach ($users as $use){
            $cards[] = $use->card_id;
        }
        if(count($users) == 0){
            response::jsonEncode(200,'success',['status'=>'身份证号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'身份证号['.implode(':',$cards).']已被占用']);
        }
    }

    //检查用户身份证号唯一
    public function getCardUsersPhone(Request $req){
        $card_ids=$req->input('phones');
        $users=User::whereIn('mobile',$card_ids)->get();
        $cards = [];
        foreach ($users as $use){
            $cards[] = $use->mobile;
        }

        if(count($users) == 0){
            response::jsonEncode(200,'success',['status'=>'身份证号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'手机号['.implode(':',$cards).']已被占用']);
        }
    }

    //检查公司注册号唯一
    public function getCardCompany(Request $req){
        $card_id=trim($req->card_id);
        $user=User::where('card_id',$card_id)->where('class',2)->first();
        if(empty($user)){
            response::jsonEncode(200,'success',['status'=>'企业注册号可以使用']);
        }else{
            response::jsonEncode(400,'error',['status'=>'企业注册号被占用']);
        }
    }
    public function userpAdd(Request $req){
        $user=new User();
        $user->staff_id=trim($req->staff_id);
        $user->username=$req->username;
        $user->mobile=trim($req->mobile);
        $user->card_id=$req->card_id;
        $old_user=User::where('card_id',$req->card_id)->where('class',1)->first();
        if(!empty($old_user)){
            response::jsonEncode(400,'error',['status'=>'身份证号被占用']);
        }
        $user->class=1;
        $user->time=date('Y-m-d',time());
        $user->delstatus=0;
        if($user->save()){
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
    public function usercAdd(Request $req){
        $user=new User();
        $user->staff_id=trim($req->staff_id);
        $user->username=trim($req->username);
        $user->mobile=trim($req->mobile);
        $user->card_id=trim($req->card_id);
        $user->class=2;
        $user->time=date('Y-m-d',time());
        $user->delstatus=0;
        $old_user=User::where('card_id',trim($req->card_id))->where('class',2)->first();
        if(!empty($old_user)){
            response::jsonEncode(400,'error',['status'=>'注册号被占用']);
        }
        if($user->save()){
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

    /**
     * 联名客户添加
     * @param Request $req
     */
    public function usercJAdd(Request $req){

        $userArray = $req->input('users');

        DB::beginTransaction();
        $mainUserId = User::insertGetId([
            'staff_id'=>$userArray[0]['staff_id'],
            'username'=>$userArray[0]['name'],
            'mobile'=>$userArray[0]['phone'],
            'card_id'=>$userArray[0]['card'],
            'class'=>3,
            'time'=>date('Y-m-d',time()),
            'delstatus'=>0,
            'ismain'=>1,
        ]);

        if(!$mainUserId){
            DB::rollBack();
            response::jsonEncode(400,'error',['status'=>'用户添加失败']);
        }else{
            $users = [];
            for($i = 1 ; $i<count($userArray) ; $i++){
                    $userData = [
                        'staff_id'=>$userArray[$i]['staff_id'],
                        'username'=>$userArray[$i]['name'],
                        'mobile'=>$userArray[$i]['phone'],
                        'card_id'=>$userArray[$i]['card'],
                        'class'=>3,
                        'time'=>date('Y-m-d',time()),
                        'delstatus'=>0,
                        'ismain'=>0,
                        'main_user_id'=>$mainUserId
                    ];
                $users[] = $userData;
            }
            $status =  User::insert($users);
            if($status){
                DB::commit();
                response::jsonEncode(200,'error',['status'=>'用户添加失败']);
            }else{
                DB::rollBack();
                response::jsonEncode(400,'error',['status'=>'用户添加失败']);
            }
        }



    }

    public function findAllLUser(){
        return User::where('class',3)->where('ismain',0)->get();
    }

    public function userList(Request $req,$staff_id,$page=1){
        $limit=10;
        $staff=Staff::where('id',$staff_id)->where('role','>=',5)->first();
        $userall=User::where('staff_id',$staff->id)->get();
        $num=$userall->count();
        $pageall=ceil($num/$limit);
        if(!$staff){
             response::jsonEncode(400,'error',['status'=>'当前成员不存在']);
        }

        $jUsersList = $this->findAllLUser();

        $users=User::where('staff_id',$staff->id)->forPage($page,$limit)->orderBy('id','desc')->get();
            $count = 0;
            foreach($users as $k=>$v){

                $jUsers = [];
                $userCount = 0;
                if($v->class == 3 && $v->ismain == 1){
                    foreach ($jUsersList as $ju){
                        if($ju->main_user_id == $v->id){
                            $jUsers[$userCount] = [
                                'user_id'   =>$ju->id,
                                'username'  =>$ju->username,
                                'mobile'    =>$ju->mobile,
                                'card_id'   =>$ju->card_id,
                            ];
                            $userCount++;
                        }
                    }
                }
                if($v->class != 3 || $v->ismain == 1){
                    $data[$count]=[
                        'staff_id'  =>$v->staff_id,
                        'user_id'   =>$v->id,
                        'username'  =>$v->username,
                        'mobile'    =>$v->mobile,
                        'card_id'   =>$v->card_id,
                        'class'     =>$v->class,
                        'jUser' => $jUsers
                    ];
                    $count ++;
                }
            }
            if(!empty($data)){
                $date=[
                    'num'=>$num,
                    'pageall'=>$pageall,
                    'data'=>$data,
                ];
                response::jsonEncode(200,'success',$date);
            }else{
                response::jsonEncode(400,'error',['status'=>'当前没有客户']);
            }


    }

    //超级管理员访问客户列表
    public function userAdminList(Request $req,$page=1){
        $limit=10;
        $users=User::forPage($page,$limit)->orderBy('id','desc')->get();
        $user=User::all();
        $num=$user->count();
        $pageall=ceil($num/$limit);

        $jUsersList = $this->findAllLUser();
        $count = 0;
        foreach($users as $k=>$v){
            $jUsers = [];
            $userCount = 0;
            if($v->class == 3 && $v->ismain == 1){
                foreach ($jUsersList as $ju){
                    if($ju->main_user_id == $v->id){
                        $jUsers[$userCount] = [
                            'user_id'   =>$ju->id,
                            'username'  =>$ju->username,
                            'mobile'    =>$ju->mobile,
                            'card_id'   =>$ju->card_id,
                        ];
                        $userCount++;
                    }
                }
            }
            if($v->class != 3 || $v->ismain == 1){
                $data[$count]=[
                    'staff_id'  =>$v->staff_id,
                    'user_id'   =>$v->id,
                    'username'  =>$v->username,
                    'mobile'    =>$v->mobile,
                    'card_id'   =>$v->card_id,
                    'class'     =>$v->class,
                    'jUser' => $jUsers
                ];
                $count ++;
            }
        }

        if(!empty($data)){
            $date=[
                'num'=>$num,
                'pageall'=>$pageall,
                'data'=>$data,
            ];
            response::jsonEncode(200,'success',$date);
        }else{
            response::jsonEncode(400,'error',['status'=>'当前没有客户']);
        }

    }

    //顾问管理员访问客户列表
    public function userAdminsList(Request $req,$admin_id,$page=1){
            $limit=10;
            $staffs=Staff::where('pid',$req->admin_id)->get();
            foreach($staffs as $k=>$v){
                $staff[]=$v->id;
            }
            if(empty($staff)){
                response::jsonEncode(400,'error',['status'=>'当前顾问管理员下没有所属顾问']);
            }
            $users=User::whereIn('staff_id',$staff)->orderBy('id','desc')->skip(($page-1)*$limit)->take($limit)->get();
            $user=User::whereIn('staff_id',$staff)->get();
            $num=$user->count();
            $pageall=ceil($num/$limit);

        $jUsersList = $this->findAllLUser();
        $count = 0;
        foreach($users as $k=>$v){
            $jUsers = [];
            $userCount = 0;
            if($v->class == 3 && $v->ismain == 1){
                foreach ($jUsersList as $ju){
                    if($ju->main_user_id == $v->id){
                        $jUsers[$userCount] = [
                            'user_id'   =>$ju->id,
                            'username'  =>$ju->username,
                            'mobile'    =>$ju->mobile,
                            'card_id'   =>$ju->card_id,
                        ];
                        $userCount++;
                    }
                }
            }
            if($v->class != 3 || $v->ismain == 1){
                $data[$count]=[
                    'staff_id'  =>$v->staff_id,
                    'user_id'   =>$v->id,
                    'username'  =>$v->username,
                    'mobile'    =>$v->mobile,
                    'card_id'   =>$v->card_id,
                    'class'     =>$v->class,
                    'jUser' => $jUsers
                ];
                $count ++;
            }
        }


            if(!empty($data)){
                $date=[
                    'num'=>$num,
                    'pageall'=>$pageall,
                    'data'=>$data,
                ];
                response::jsonEncode(200,'success',$date);
            }else{
                response::jsonEncode(401,'error',['status'=>'当前所属成员没有客户']);
            }

    }

    public function userSelect(Request $req){
        $id=$req->id;
        $user=User::find($id);
        $data=[
            'id' => $user->id,
            'username' => $user->username,
            'mobile' => $user->mobile,
            'card_id' => $user->card_id,
            'class' => $user->class,
        ];
        $code=200;
        $message='success';
        response::jsonEncode($code,$message,$data);
    }
    public function userEdit(Request $req){
        $id=$req->id;
        $user=User::find($id);
        $user->username=$req->username;
        if($req->mobile==$user->mobile){
            $user->mobile = $user->mobile;
        }else{
            $olduser=User::where('mobile',$req->mobile)->first();
            if(empty($olduser)){
                $user->mobile=$req->mobile;
            }else{
                response::jsonEncode(400,'error',['status'=>'手机号被占用']);
            }
        }
        //$user->mobile=$req->mobile;
        $user->card_id=$req->card_id;
        $user->class=$req->class;
        $user->time=date('Y-m-d',time());

        if($user->save()){
            $data=['status'=>'修改成功'];
            response::jsonEncode(200,'success',$data);
        }else{
            $data=['status'=>'修改失败'];
            response::jsonEncode(200,'false',$data);
        }
    }

    public function editJuser(Request $req){
        $uid = $req->input('uid');
        $username = $req->input('username');
        $phone  = $req->input('phone');
        $card = $req->input('card');
        $status = User::where('id',$uid)->update(['username'=>$username,'mobile'=>$phone,'card_id'=>$card]);
        if($status){
            response::jsonEncode(200,'success','');
        }else{
            response::jsonEncode(201,'success','用户信息修改失败');
        }
    }

    public function userDel(Request $req){
        $id=$req->id;
        User::where('id',$id)->delete();
        $data=['status'=>'删除成功'];
        response::jsonEncode(200,'success',$data);
    }

    public function userDateSel(Request $req){

        $user_id=$req->user_id;
        $user = User::find($user_id);
        //获得session管理员id
        $staff=Staff::find($user->staff_id);
        $project=Project::find($staff->project_id);
        //dd($project);
        //dd($project);
        if(empty($staff->project_id)){
            response::jsonEncode(400,'error',['status'=>'当前操作人员无关联项目']);
        }else{
            $banks=Bank::where('project_id',$staff->project_id)->get();
            $bank=[];
            foreach($banks as $k=>$v){
                $bank[]=$v->bank;
            }
            $banklist = RBank::whereIn('bank_id',$bank)->get();
        }
        $timeslot = DB::table('timeslots')->get();
        $data=[
            'project_id'=>$staff->project_id,
            'project_name'=>$project->name,
            'banks' => $banklist ,
            'timeslot'=>$timeslot,
        ];
        response::jsonEncode(200,'success',$data);
    }

    public function finJUser(Request $req){
        $mainUserId = $req->input('mainID');
        $mainUser = User::find($mainUserId);
        $jUsers = User::where('main_user_id',$mainUserId)->get();
        $users = array();
        $users[] = $mainUser;
        foreach ($jUsers as $ju){
            $users[] = $ju;
        }
        response::jsonEncode(200,'success',$users);
    }


    public function findClass(Request $req){
        $user_id=$req->input('user_id');
        $user = User::find($user_id);
        response::jsonEncode(200,'success',$user->class);
    }


    public function userDateTimeslots(Request $req){
        $date=$req->date;
        $timeslot = DB::table('timeslots')->where('id',$req->id)->first();
        //foreach($timeslot as $k => $v) {
        $row = DB::table('reserves_vs_timeslots')->where('date', '=', $date)->where('timeslot_id', '=', $req->id)->get();
        $num =count($row);
        if($num<$timeslot->persons){
            $data=[
                'persons' => $timeslot->persons-$num,
            ];
            response::jsonEncode(200,'success',$data);
        }else{
            response::jsonEncode(400,'success',['status'=>'当前时间段预约人数已满']);
        }
    }


    //新建预约
    public function userDate(Request $req){
        $staff_id=$req->staff_id;
        $staff=Staff::find($staff_id);

        $reserve = new Reserve();
        if($staff->project_id && ( !empty($req->lou_fen) || $req->lou_fen === '0' ) && ( !empty($req->lou_hao) || $req->lou_hao === '0' )&& ( !empty($req->unit)|| $req->unit === '0' ) && (  !empty($req->number)|| $req->number === '0' )&& !empty($req->payfor) && $req->total_money&& !empty($req->date) && !empty($req->timeslot_id) && isset($req->discount)&& isset($req->pay_status)&& isset($req->sign_zip)&& isset($req->reserve_class)){
            $reserve_list=Reserve::where('project_id',$staff->project_id)->where('lou_fen',trim($req->lou_fen))->where('lou_hao',trim($req->lou_hao))->where('unit',trim($req->unit))->where('number',trim($req->number))->get();
            //循环
            foreach ($reserve_list as $reserve_one){
                if($reserve_one->status != 4){
                    if($reserve_one->user_id != $req->user_id){//判断是否为本人再次预约
                        response::jsonEncode(400,'error',['status'=>'房源已被其他客户锁定，如需预约请将之前该房源预约作废']);
                        return;
                    }
                }
            }
            foreach ($reserve_list as $reserve_one){

                    if($reserve_one->user_id == $req->user_id && $reserve_one->reserve_class == $req->reserve_class){//判断是否为本人再次预约
                        response::jsonEncode(400,'error',['status'=>'该预约类型已存在，不能重复预约']);
                        return;
                    }

            }

                $reserve->user_id = $req->user_id;
                $reserve->staff_id = $req->staff_id;

                $reserve->project_id = $staff->project_id;
                $reserve->lou_fen = trim($req->lou_fen);
                $reserve->lou_hao = trim($req->lou_hao);
                $reserve->unit = trim($req->unit);
                $reserve->number = trim($req->number);
                $reserve->payfor = $req->payfor;
                if($req->payfor == 1){
                    $reserve->total_money = $req->total_money;
                    $reserve->first_money = 0;
                    $reserve->loan_money = 0;
                    $reserve->pay_bank=0;
                }elseif($req->payfor==2 || $req->payfor==3|| $req->payfor==4 ){
                    if($req->first_money && $req->loan_money && !empty($req->bank_name)){
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
                    response::jsonEncode(401,'error',['status'=>'付款方式选择错误']);
                }

                //$reserve->pay_bank=$req->pay_bank;

                $reserve->date=$req->date;
                $shijian_xianzhi = date('Y-m-d', strtotime('+1 day'));
                //response::jsonEncode(412,'error',['status'=>$shijian_xianzhi]);
                //response::jsonEncode(412,'error',['status'=>$reserve_center]);
                $timestap = strtotime($req->date);
                $req_date = date('Y-m-d', $timestap);
//                dd($req_date,$timestap,$req->date,$shijian_xianzhi,$reserve_center);
                if($req_date < $shijian_xianzhi && $reserve_center != 1){
                    response::jsonEncode(412,'error',['status'=>'当日不能发起预约,请从明天开始选择!']);
                }
                //response::jsonEncode(412,'error',['status'=>$shijian_xianzhi]);

                //调用 预约时段接口
                $timeslot = DB::table('timeslots')->where('id',$req->timeslot_id)->first();


                $reserve->reserve_stime=$timeslot->started;
                $reserve->reserve_etime=$timeslot->ended;


                $row = DB::table('reserves_vs_timeslots')->where('date', '=', $req->date)->where('timeslot_id', '=', $timeslot->id)->get();
                $num =count($row);
                if($num<$timeslot->persons){
                    //结束调用
                    $reserve->discount=$req->discount;
                    $reserve->pay_status=$req->pay_status;
                    $reserve->sign_zip=$req->sign_zip;
                    $reserve->reserve_class=$req->reserve_class;
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
                    $reserve->status=1;
                    $reserve->progress=1;
                    $reserve->sign_staff_id='';
                    if($req->reserve_center==1){

                        $reserve->reserve_center=1;
                    }else{

                        $reserve->reserve_center=0;
                    }
                    $reserve->halt=0;
                    $reserve->compact=0;
                    $reserve->compact_notes=0;
                    $reserve->special=$req->special;
                    $reserve->notes=$req->notes;
                    if($reserve->save()){
                        $arr['created'] = time();
                        $arr=['timeslot_id'=>$timeslot->id,'reserve_id'=>$reserve->id,'date'=>$req->date];
                        DB::table('reserves_vs_timeslots')->insert($arr);
                        $wx=new WxController();
                        $notice=$wx->noticeMB($reserve->id);
                        if($notice==1){
                            response::jsonEncode(200,'success',['status'=>'添加并推送成功']);
                        }else{
                            response::jsonEncode(200,'success',['status'=>'添加成功']);
                        }
                    }else{
                        $code=400;
                        $message='false';
                        $data=['status'=>'添加失败'];
                        response::jsonEncode($code,$message,$data);
                    }
                }else{
                    response::jsonEncode(400,'success',['status'=>'当前时间段预约人数已满']);
                }

        }else{
            response::jsonEncode(401,'error',['status'=>'预约信息不完整']);
        }


    }


    public function setRTime(Request $req){
        $time  = $req->input('time');
        $status = RTime::where('time_id',env('TIME_ID'))->update(['time'=>$time]);
        if($status){
            response::jsonEncode(200,'error',['status' => '此房源已经发起正签']);
        }else{
            response::jsonEncode(201,'error',['status' => '可预约时间设置失败']);
        }
    }

    public function getRTime(Request $req){

        $status = RTime::where('time_id',env('TIME_ID'))->first();
        if($status){
            response::jsonEncode(200,'error',$status->time);
        }else{
            response::jsonEncode(201,'error',['status' => '可预约时间获取失败']);
        }
    }

    //新建正签预约
    public function userReserveClassDate(Request $req){
        //$req->reserve_id,$req->date,$req->timeslot_id,$req->special,$req->notes,
        $reserve_id=$req->reserve_id;
        $old_reserve = Reserve::find($reserve_id);
        //同一套房源只能有一次正签,一次草签
        $res = Reserve::where('project_id',$old_reserve->project_id)->where('lou_fen',trim($old_reserve->lou_fen))->where('lou_hao',trim($old_reserve->lou_hao))->where('unit',trim($old_reserve->unit))->where('number',trim($old_reserve->number))->get();
        $num = '';
        foreach ($res as $k => $v) {
            if($v->reserve_class == 2){
                $num = 1;
            }
        }
        if($old_reserve->reserve_class == 2 || $num == 1){
            response::jsonEncode(410,'error',['status' => '此房源已经发起正签']);
        }
        $reserve = new Reserve();
        if( !empty($req->date) && !empty($req->timeslot_id) ){
                $reserve->user_id = $old_reserve->user_id;
                $reserve->staff_id = $old_reserve->staff_id;
                $reserve->project_id = $old_reserve->project_id;
                $reserve->lou_fen = trim($old_reserve->lou_fen);
                $reserve->lou_hao = trim($old_reserve->lou_hao);
                $reserve->unit = trim($old_reserve->unit);
                $reserve->number = trim($old_reserve->number);
                $reserve->payfor = $old_reserve->payfor;
                $reserve->total_money = $old_reserve->total_money;
                $reserve->first_money = $old_reserve->first_money;
                $reserve->loan_money = $old_reserve->loan_money;
                $reserve->pay_bank=0;
                $reserve->bank_name=$old_reserve->bank_name;
                $reserve->date=$req->date;
//                $shijian_xianzhi = date('Y-m-d', strtotime('+1 day'));
//                //response::jsonEncode(412,'error',['status'=>$shijian_xianzhi]);
//                if($reserve->date < $shijian_xianzhi && $req->reserve_center != 1){
//                    response::jsonEncode(412,'error',['status'=>'当日不能发起预约,请从明天开始选择!']);
//                }
                //调用 预约时段接口
                $timeslot = DB::table('timeslots')->where('id',$req->timeslot_id)->first();
                $reserve->reserve_stime=$timeslot->started;
                $reserve->reserve_etime=$timeslot->ended;
                $row = DB::table('reserves_vs_timeslots')->where('date', '=', $req->date)->where('timeslot_id', '=', $timeslot->id)->get();
                $num =count($row);
                if($num<$timeslot->persons){
                    //结束调用
                    $reserve->discount=$old_reserve->discount;
                    $reserve->pay_status=$old_reserve->pay_status;
                    $reserve->sign_zip=$old_reserve->sign_zip;
                    $reserve->reserve_class=2;
                    $reserve->status=1;
                    $reserve->progress=1;
                    $reserve->sign_staff_id='';
                    if($req->reserve_center==1){
                        $reserve->reserve_center=1;
                    }else{
                        $reserve->reserve_center=0;
                    }
                    $reserve->halt=0;
                    $reserve->compact=0;
                    $reserve->compact_notes=0;
                    $reserve->special=$req->special;//车牌
                    $reserve->notes=$req->notes;//备注
                    if($reserve->save()){
                        $arr['created'] = time();
                        $arr=['timeslot_id'=>$timeslot->id,'reserve_id'=>$reserve->id,'date'=>$req->date];
                        DB::table('reserves_vs_timeslots')->insert($arr);
                        $wx=new WxController();
                        $notice=$wx->noticeMB($reserve->id);
                        if($notice==1){
                            response::jsonEncode(200,'success',['status'=>'添加并推送成功']);
                        }else{
                            response::jsonEncode(200,'success',['status'=>'添加成功']);
                        }
                    }else{
                        $code=400;
                        $message='false';
                        $data=['status'=>'添加失败'];
                        response::jsonEncode($code,$message,$data);
                    }
                }else{
                    response::jsonEncode(400,'success',['status'=>'当前时间段预约人数已满']);
                }
        }else{
            response::jsonEncode(401,'error',['status'=>'预约信息不完整']);
        }


    }


    public function userListKey(Request $req){
        $key=$req->key;
        $key=trim($key);
        if(is_numeric($key)){
            $findusers=User::where('mobile',$key)->get();
            //查询联名客户
            $users=array();
            foreach ($findusers as $us){
                if($us->class == 3 && $us->ismain==0){
                    //查询
                    $fU = User::where('id',$us->main_user_id)->first();
                    if($fU)$users[] = $fU;
                }else{
                    $users[] = $us;
                }
            }

            $data=array();
            $jUsersList = $this->findAllLUser();

            $count = 0;
            foreach($users as $k=>$v){
                $jUsers = [];
                $userCount = 0;
                if($v->class == 3 && $v->ismain == 1){
                    foreach ($jUsersList as $ju){
                        if($ju->main_user_id == $v->id){
                            $jUsers[$userCount] = [
                                'user_id'   =>$ju->id,
                                'username'  =>$ju->username,
                                'mobile'    =>$ju->mobile,
                                'card_id'   =>$ju->card_id,
                            ];
                            $userCount++;
                        }
                    }
                }
                if($v->class != 3 || $v->ismain == 1){
                    $data[$count]=[
                        'staff_id'  =>$v->staff_id,
                        'user_id'   =>$v->id,
                        'username'  =>$v->username,
                        'mobile'    =>$v->mobile,
                        'card_id'   =>$v->card_id,
                        'class'     =>$v->class,
                        'jUser' => $jUsers
                    ];
                    $count ++;
                }
            }
            if(empty($data)){
                response::jsonEncode(301,'error',['status'=>'该号码没有用户信息']);
            }
            response::jsonEncode(200,'success',$data);
        }else{
            $findusers=User::where('username',$key)->get();
            $users=array();
            foreach ($findusers as $us){
                if($us->class == 3 && $us->ismain==0){
                    //查询
                    $fU = User::where('id',$us->main_user_id)->first();
                    if($fU)$users[] = $fU;
                }else{
                    $users[] = $us;
                }
            }

            $data=array();
            foreach($users as $k=>$v){
                $jUsersList = $this->findAllLUser();
                $count = 0;
                foreach($users as $k=>$v){
                    $jUsers = [];
                    $userCount = 0;
                    if($v->class == 3 && $v->ismain == 1){
                        foreach ($jUsersList as $ju){
                            if($ju->main_user_id == $v->id){
                                $jUsers[$userCount] = [
                                    'user_id'   =>$ju->id,
                                    'username'  =>$ju->username,
                                    'mobile'    =>$ju->mobile,
                                    'card_id'   =>$ju->card_id,
                                ];
                                $userCount++;
                            }
                        }
                    }
                    if($v->class != 3 || $v->ismain == 1){
                        $data[$count]=[
                            'staff_id'  =>$v->staff_id,
                            'user_id'   =>$v->id,
                            'username'  =>$v->username,
                            'mobile'    =>$v->mobile,
                            'card_id'   =>$v->card_id,
                            'class'     =>$v->class,
                            'jUser' => $jUsers
                        ];
                        $count ++;
                    }
                }
            }
            if(empty($data)){
                response::jsonEncode(301,'error',['status'=>'该姓名没有用户信息']);
            }else{
                response::jsonEncode(200,'success',$data);
            }

        }
    }
}
