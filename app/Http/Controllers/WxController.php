<?php

namespace App\Http\Controllers;
use App\Admin;
use App\Project;
use App\Reserve;
use App\Staff;
use App\UserProgress;
use App\Wx_answer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Overtrue\Wechat\Server;
use Overtrue\Wechat\Message;
use Overtrue\Wechat\User as WxUser;
use Overtrue\Wechat\Menu;
use Overtrue\Wechat\MenuItem;
use Overtrue\Wechat\Notice;
use App\User;
use App\Wx_user;
use App\Suggest;
use response;
use DB;




class WxController extends Controller
{
    private $appid;
    private $appsecret;
    private $token;

    public function __construct()
    {
        $this->appid=env('WX_APPID');
        $this->appsecret=env('WX_APPSECRET');
        $this->token=env('WX_TOKEN');
//        $menuServer=new Menu($this->appid,$this->appsecret);
//        $menuServer->get();// 请求微信服务器
        //$this->middleware('verify', ['except' => 'index']);
    }


    public function index() {

        // $encodingAESKey 可以为空 服务器验证
        $server = new Server( $this->appid , $this->token );
        $server->on('event' , 'subscribe' , [$this , 'guanzhu']);
        $server->on('event' , 'unsubscribe' , [$this , 'qxgz']);
        $server->on('message','text',[$this,'keyword']);
        $server->on('event','LOCATION',[$this,'map']);
        $server->on('event','click',[$this,'aboutAnswer']);
        return $server->serve();
    }

    public function guanzhu($event)
    {
        // 取出粉丝的个人信息
        //$this->setWechatMenu();
        $openid=$event->FromUserName;
        $server=new WxUser($this->appid,$this->appsecret);
        $wxuser=$server->get($event->FromUserName);
        $old_username=Wx_user::where('wx_openid',$openid)->first();
        $wx_answers=Wx_answer::where('num',0)->orderBy('id','asc')->get();
        $welcome="欢迎关注!您可以输入相关序号进行自动回复:\n";
        foreach($wx_answers as $K=>$v){
            $welcome.=$v->keywords."\n";
        }
        if($old_username){
            $old_username->wx_nick = $wxuser->nickname;
            $old_username->wx_sex = $wxuser->sex;
            $old_username->wx_headimgurl =$wxuser->headimgurl;
            $old_username->status=1;
            $old_username->time = date('Y-m-d',time());
            $old_username->save();
            return $welcome;
        }else{
            $wx_user=new Wx_user();
            $wx_user->wx_openid = $event->FromUserName;
            $wx_user->wx_nick = $wxuser->nickname;
            $wx_user->wx_sex = $wxuser->sex;
            $wx_user->wx_headimgurl =$wxuser->headimgurl;
            $wx_user->status = 1;
            $wx_user->time = date('Y-m-d',time());
            $wx_user->save();
//            $user=new User();
//            $user->openid=$event->FromUserName;
            if($wx_user->save()){
                return $welcome;
            }

        }
    }
    //取消关注操作
    public function qxgz($event) {
        $openid = $event->FromUserName;
        $user = Wx_user::where('wx_openid' , $openid)->first();

        if($user) {
            $user->status = 0;
            $user->save();
        }
    }
    //自定义回复
    public function keyword($event)
    {
        $keyword = trim($event->Content);//用户输入
        //绑定
        $get_key = mb_substr($keyword, 0, 2, 'utf-8');

        //回答数字
        //$num_key=strlen(is_numeric($keyword));
        $wx_answer = DB::table('wx_answers')->where('keywords', $keyword)->first();//数据库信息
        if ($wx_answer) {
            $answers = $wx_answer->answers;
            return Message::make('text')->Content($answers);
        } elseif ($get_key == 'BD') {
            $mobile = mb_substr($keyword, 2, 11, 'utf-8');
            if (strlen($mobile) == 11) {
                $admin = Admin::where('mobile', $mobile)->whereIn('role', [1, 2])->first();
                if ($admin) {
                    $admin->openid = $event->FromUserName;
                    if ($admin->save()) {

                        return Message::make('text')->Content('绑定成功!');
                    }
                } else {
                    return Message::make('text')->Content('您的权限不够,无法绑定!');

                }
            } else {
                return Message::make('text')->Content('绑定手机号码为11位!');
            }

        }else{
            $wx_answers=Wx_answer::where('num',0)->orderBy('id','asc')->get();
            $welcome="您可以输入相关序号进行自动回复:\n";
            foreach($wx_answers as $K=>$v){
                $welcome.=$v->keywords."\n";
            }
            return Message::make('text')->Content("$welcome");
        }

    }
    //自定义菜单
    public function setWechatMenu(){

        $menuServer=new Menu($this->appid,$this->appsecret);

        $button1 = new MenuItem("更多");

//        $button2 = new MenuItem("叫号查询");
//        $button3 = new MenuItem("我的预约");
        $menus = array(

//            new MenuItem("一键导航",'view',''),
            new MenuItem('一键导航','view',env('WX_URL').'/wx/map'),
            new MenuItem("我的预约",'view',env('WX_URL').'/wx/get/my/reserve'),
//            new MenuItem("投诉建议",'view',env('WX_URL').'/wx/suggesstion'),
            $button1->buttons(array(
                new MenuItem('进度查询','view',env('WX_URL').'/userProgress'),
                new MenuItem('签约流程','view',env('WX_URL').'/wx/progress'),
                new MenuItem('注意事项','view',env('WX_URL').'/wx/notices'),
                new MenuItem('常见问题','click','aboutAnswer'),
                //new MenuItemx('投诉建议','view',env('WX_URL').'/wx/suggesstion'),
                new MenuItem('联系我们','view',env('WX_URL').'/wx/address'),
            )),
        );

        try {
            $menuServer->set($menus);// 请求微信服务器
            echo '设置成功！';
        } catch (\Exception $e) {
            echo '设置失败：' . $e->getMessage();
        }
        
    }
    public function aboutAnswer(){
        $wx_answers=Wx_answer::where('num',0)->orderBy('id','asc')->get();
        $welcome="您可以输入相关序号进行自动回复:\n";
        foreach($wx_answers as $K=>$v){
            $welcome.=$v->keywords."\n";
        }
        return Message::make('text')->Content("$welcome");
    }
    // 循环领导的id
    public function selLeader(){
        $admins=Admin::whereNotNull('openid')->get();
        foreach($admins as $k=>$v){
            $admin[]=[
                'admin_id' => $v->id,
                'admin_name' => $v->name,
            ];
        }
        if(empty($admin)){
            response::jsonEncode(400,'error',['status'=>'没有领导绑定微信']);

        }else{
            response::jsonEncode(200,'error',$admin);

        }
    }
    //自定义报表
    public function noticeLeader(Request $req){
        //拿到领导的主键 发送 通知
        //var_dump($_POST);
        $user_id=$req->admin_id;
        //$user_id=2;
        $keyword2=$req->contents;
        //$keyword2=1;
        $keyword1=$req->title;
        //$keyword1=222;

        if((!$user_id) && (!$keyword2) && (!$keyword1)){
            response::jsonEncode(401,'error',['status'=>'post数组没有获取到']);
        }
        $admin=Admin::find($user_id);
        $notice=new Notice($this->appid,$this->appsecret);
        if(empty($admin->openid)){
            response::jsonEncode(400,'error',['status'=>'领导未绑定微信']);
        }else{
            $userId=$admin->openid;
            //消息类型
            $templateId=env('WX_TEMP');
            $color='#FF0000';

            $remark="竭诚为您服务!";
            $url='';
            if($user_id && $keyword2){
                $data=[
                    "first" => '',
                    "keyword1" =>'领导推送',
                    "keyword2" =>$keyword1,
                    "remark"       =>$keyword2,
                ];
                $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
                if(is_numeric($notice)){
                    response::jsonEncode(200,'success',['status'=>'发送成功']);
                }
            }
        }
    }


    public function noticeExcle($phone,$username,$openid){
        //查询用户信息


            $notice=new Notice($this->appid,$this->appsecret);
            $keyword1 = "尊敬的( $username )";
            $keyword3 = Carbon::now();
            $remark = "您签约的项目有了新的进度，请前往[更多]->[进度查询]中进行查看";
            $data=[
                "first" => '进度更新提醒',
                "keyword1" =>$keyword1,
                "keyword2" =>$phone,
                "keyword3" =>$keyword3,
                "remark"  =>$remark,
            ];
            $templateId=env('WX_TEMP');
        $url='';
        $color='#FF0000';
            $notice=$notice->send($to=$openid,$templateId,$data,$url,$color);


    }

    //模版消息
    public function noticeMB($reserve_id){
        //拿到预约信息的主键 发送 通知
        $reserve=Reserve::find($reserve_id);
        $project=Project::find($reserve->project_id);
        //初始信息
        //dd($reserve);
        $date='('.$reserve->date.')('.$reserve->reserve_stime.'-'.$reserve->reserve_etime.')';
        $bank=response::banks($reserve->pay_bank);
        //dd($bank);
        $house=$project->name.' '.$reserve->lou_fen.','.$reserve->lou_hao.'-'.$reserve->unit.'-'.$reserve->number;
        $notice=new Notice($this->appid,$this->appsecret);
        $user=DB::table('users')->where('id',$reserve->user_id)->first();
        if($reserve->payfor==1){
            $payfor='一次性付款';
        }elseif($reserve->payfor==2){
            $payfor='银行按揭';
        }elseif($reserve->payfor==3){
            $payfor='公积金';
        }elseif($reserve->payfor==4){
            $payfor='组合贷';
        }
        if($reserve->payfor==1){
        $keyword1="尊敬的客户您好,您已成功预约办理( $house ) 的签约手续.预约时间为 $date 。请您带齐相关资料按时到场办理。"."\n"."不明事宜可致电010-65068488。";
        }else{
        //$keyword1="亲爱的客户您好,您已成功预约 $date 办理( $house ) 的签约手续,选择 $bank $payfor 方式";
        $keyword1="尊敬的客户您好,您已成功预约办理( $house ) 的签约手续.预约时间为 $date 。请您带齐相关资料按时到场办理。"."\n"."不明事宜可致电010-65068488。";
        }
        $keyword2='温馨提示:签约流程、注意事项、常见问题等可点击更多获取信息。';
        if(!$user->openid){
            return 2;
        }else{
        $userId=$user->openid;
        //消息类型
        $templateId=env('WX_TEMP');
        $color='#FF0000';
//
        //$remark="欢迎您选择我们,我们将竭诚为您服务!";
        $remark="温馨提示:签约流程、注意事项、常见问题等可点击更多获取信息。";
        $url='';
        if($reserve->progress ==1 && $reserve->status ==1){
            $data=[
                "first" => $keyword1,
                "keyword1" =>'签约',
                "keyword2" =>'预约成功',
                "remark"  =>$keyword2,
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif( $reserve->progress ==2 ){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源)预约已失效。请联系您的销售顾问重新为您预约",
                "keyword1" =>"签约",
                "keyword2" =>"预约失效",
                "remark"       =>"",
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress ==0 && $reserve->status ==2){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源)预约已取消。请联系您的销售顾问重新为您预约",
                "keyword1" =>"签约",
                "keyword2" =>"预约已取消",
                "remark"       =>"",
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress ==1 && $reserve->status ==3){
            $data=[
                "first" => "尊敬的客户您好,您已重新预约办理( $house 房源) 的签约手续.预约时间为 $date 。请您带齐相关资料按时到场办理。"."\n"."不明事宜可致电010-65068488。",
                "keyword1" =>"签约",
                "keyword2" =>"重新预约",
                "remark"       =>"$remark",
            ];

            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress ==3 && $reserve->halt==0){
            $data=[
                "first" => '尊敬的客户您好,您已成功取号,请留意广播和显示屏叫号,等候办理!',
                "keyword1" =>"签约",
                "keyword2" =>"签约办理等待中",
                "remark"       =>"",
            ];

            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif(($reserve->progress ==3 && $reserve->halt ==2 &&$reserve->status==1) ||($reserve->progress ==3 && $reserve->halt ==2 &&$reserve->status==3)){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源) 签约恢复办理。",
                "keyword1" =>"签约",
                "keyword2" =>"签约恢复",
                "remark"       =>"",
            ];

            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif(($reserve->progress ==3 && $reserve->halt ==1 &&$reserve->status==1) ||($reserve->progress ==3 && $reserve->halt ==1 &&$reserve->status==3)){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源) 签约暂时停止办理,请与工作人员沟通确认问题后重新预约办理。",
                "keyword1" =>"签约",
                "keyword2" =>"签约暂停",
                "remark"       =>"",
            ];

            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress ==4 && $reserve->compact_notes==0 ){
            $data=[
                "first" => "尊敬的客户您好,恭喜您的( $house 房源) 签约手续办理成功,感谢您的配合与支持,祝您生活愉快!",
                "keyword1" =>"签约",
                "keyword2" =>"签约完成",
                "remark"       =>"",
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress == 4 && $reserve->compact_notes==1 ){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源) 合同已完成,请您于周五至周日(10:00-17:00)到首创置业梦想启航站领取。",
                "keyword1" =>"领取合同",
                "keyword2" =>"预约成功",
                "remark"   =>"领取合同时需您携带身份证件原件进行核验,如需代领合同,代理人需携带双方身份证件原件及买受人手写的委托书进行领取。"."\n"."咨询电话:010-85988900"."\n"."领取地址:朝阳区东三环中路京广桥东北角首创置业梦想启航站。",
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }elseif($reserve->progress == 5 &&$reserve->compact == 1 ){
            $data=[
                "first" => "尊敬的客户您好,您的( $house 房源) 合同已领取完毕。感谢您的配合与支持,祝您生活愉快!",
                "keyword1" =>"领取合同",
                "keyword2" =>"领取成功",
                "remark"   => "",
            ];
            $notice=$notice->send($to=$userId,$templateId,$data,$url,$color);
            if(is_numeric($notice)){
                return 1;
            }
        }
        }

    }

    public function setWechatMap() {
        return view('map');
    }
    //添加投诉建议
    public function suggestAdd(Request $req){
        //$req->session()->forget('user');
        $user=$req->session()->get('user');
//        var_dump($user);
        if(!$user){
            return redirect('/wx/sugg/login');
        }
        if(empty($_GET)){
            return view('sugesstion');
        }else{
            $users=User::where('openid',$user['openid'])->first();
//            var_dump($users);

            $reserve = '';
            if(isset($users->id))
                $reserve=Reserve::where('user_id',$users->id)->first();

            if(!$reserve){
                $project_id=0;
            }else{
                $project_id=$reserve->project_id;
            }
            $suggest=new Suggest();
            $suggest->openid=$user['openid'];
            $suggest->project_id=$project_id;
            $suggest->contents=$req->sugesstion;
            $suggest->create_time=date('Y-m-d',time());
            if($suggest->save()){
                echo json_encode(['msg' => 'ok']);
            }
        }
    }
    //绑定身份页面
    public function card_get_p(Request $req){
        $wx_user=$req->session()->get('user');
        if(!$wx_user){
            return redirect('/wx/bangding/login');
        }
        if(empty($_GET)){
            return view('get_card_person');
        }else{

            //业务逻辑
            if($req->mobile && $req->card){

                $user = User::where(['mobile'=>$req->mobile,'class'=>1])->first();
                if(!$user){
                    echo json_encode(['code'=> 402, 'msg' => '没有此人']);
                }else{
                    $four_card = substr($user->card_id, -4);
                    if($four_card==$req->card){
                        if($user->openid){
                            echo json_encode(['code'=> 404, 'msg' => '该用户已绑定过微信']);
                        }else{
                            $user->openid=$wx_user['openid'];
                            $user->save();
                            echo json_encode(['code'=> 200, 'msg' => '绑定成功']);
                        }
                    }else{
                        echo json_encode(['code'=> 402, 'msg' => '没有此人']);
                    }
                }

            }

        }

    }//绑定身份页面
    public function card_get_c(Request $req){
        $wx_user=$req->session()->get('user');
        if(!$wx_user){
            return redirect('/wx/bangding/login');
        }
        if(empty($_GET)){
            return view('get_card_business');
        }else{
            //业务逻辑
            if($req->card){
                $user = User::where(['card_id'=>$req->card,'class'=>2])->first();
                if(!$user){
                    echo json_encode(['code'=> 402, 'msg' => '没有此人']);
                }else{
                if($user->card_id==$req->card){
                    $user->openid=$wx_user['openid'];
                    $user->save();
                    echo json_encode(['code'=> 200, 'msg' => '绑定成功']);
                }else{
                    echo json_encode(['code'=> 402, 'msg' => '没有此人']);
                }
                }
            }
        }


    }


    public function sendExcle(Request $req){
        $titles = $req->input('titles');
        $contents = $req->input('contents');
        //查询用户
        $phones = array();
        foreach ($contents as $c){
            $phones[] = $c[0];
        }
        //获取access_token
        $tokenUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');
        $tresult = $this->curlSimpleGet($tokenUrl);
        $access_token_data = json_decode($tresult);
        $access_token = $access_token_data->access_token;


        if(!$access_token){
            response::jsonEncode(201,'error','');
        }
        //先保存
        $users = User::whereIn('mobile',$phones)->get();
        $saveUsers = array();

            foreach ($contents as $c){
                    $sendContent = '';
                    for($i = 1 ; $i < count($c) ; $i++){
                        if($i == count($c)-1){
                            $sendContent = $sendContent.''.$titles[$i].'&'.$c[$i];
                        }else{
                            $sendContent = $sendContent.''.$titles[$i].'&'.$c[$i].'+';
                        }
                    }
                    $saveUsers[] = ['phone'=>$c[0],'content'=>$sendContent,'createAt'=>Carbon::now()];
            }

        $saveStatus =  UserProgress::insert($saveUsers);

        if(!$saveStatus){
            response::jsonEncode(201,'','');
        }
        foreach ($users as $user){
            if($user->openid){
                $this->noticeExcle($user->mobile,$user->username,$user->openid);
            }
        }
        //将进度数据存储
        response::jsonEncode(200,'success','');
    }


    public function getUserProgress(Request $req){
        $phone = $req->input('phone');
        $list = UserProgress::where('phone',$phone)->orderBy('createAt','desc')->get();
        response::jsonEncode(200,'',$list);
    }

    //查看自身预约状态
    public function get_my_reserve(Request $req){
        $wx_user=$req->session()->get('user');
        if(!$wx_user){
            return redirect('/wx/reserve/login');
        }
        $user=User::where('openid',$wx_user['openid'])->first();
        if(!$user){
            return redirect('wx/card/get/p');
        }
        $reserves=Reserve::where('user_id',$user->id)->get();
        //dd($reserves);
        foreach($reserves as $k=>$v){
            if($v->pay_bank!=0){
                $bank=response::banks($v->pay_bank);
            }else{
                $bank='无';
            }
            if($v->reserve_center==1){
                $staff=Staff::where('id',$v->staff_id)->first();
                $staff_name='签约中心';
                $staff_mobile=$staff->mobile;

            }else{
                $staff=Staff::where('id',$v->staff_id)->first();
                $staff_name=$staff->name;
                $staff_mobile=$staff->mobile;
            }
            if($v->progress==1){
                $reserve_class='签约';
                $tou='已预约';
            }elseif($v->progress==2){
                $reserve_class='签约';
                $tou='预约已经过号';
            }
            elseif($v->progress==0){
                $reserve_class='签约';
                $tou='预约已取消';
            }elseif($v->progress==3 && $v->halt==1){
                $reserve_class='签约';
                $tou='签约办理等待中(已暂停)';
            }elseif($v->progress==3){
                $reserve_class='签约';
                $tou='签约办理等待中';
            }elseif($v->progress==4){
                $reserve_class='领取合同';
                $tou='领取合同办理中';
            }elseif($v->progress==5){
                $reserve_class='领取合同';
                $tou='领取合同办理已完成';
            }
            if($v->payfor==1){
                $payfor='一次性付款';
            }elseif($v->payfor==2){
                $payfor='银行按揭';
            }elseif($v->payfor==3){
                $payfor='公积金';
            }elseif($v->payfor==4){
                $payfor='组合贷';
            }
            $project=Project::where('id',$v->project_id)->first();
            $data[]=[
            'reserve_progress' =>$tou,
            'reserve_house' =>$project->name.$v->unit.'单元,'.$v->number.'号房',
            'reserve_date' =>$v->date.' '.$v->reserve_stime.'-'.$v->reserve_etime,
            'reserve_class' =>$reserve_class,
            'reserve_staff' =>$staff_name,
            'reserve_staff_mobile' =>$staff_mobile,
            'reserve_payfor' =>$payfor,
            'reserve_bank' =>$bank,
            'reserve_notes' =>$v->notes,
            ];
        }
        if(empty($data)){
            return view('schedule');
        }else{
            return view('schedule',['data'=>$data]);
        }
    }

    public function getAddress(){
        return view('address');
    }
    public function getProgress(){
        return view('progress');
    }
    public function getNotices(){
        return view('notices');
    }
    public function forGetSession(Request $req){
        $req->session()->forget('user');
        dd($req->session());
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

    public function adminAbolishReserver(Request $req , $reserve_id){

        $reserve=Reserve::where('id',$reserve_id)->first();
        if(!$reserve){
            response::jsonEncode(302,'error',['status'=>'没有预约信息']);
        }
            $reserve->status=4;
            $reserve->progress=0;
            if($reserve->save()){
               response::jsonEncode(200,'success',['status'=>'修改成功']);
            }else{
                response::jsonEncode(400,'false',['status'=>'修改失败']);
            }
    }



}
