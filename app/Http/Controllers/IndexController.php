<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Overtrue\Wechat\Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public function suggesstion_login(Request $req){
        if($req->session()->get('user') ) {
            return redirect('/wx/suggesstion');
        }
        $auth=new Auth(env('WX_APPID'),env('WX_APPSECRET'));
        $to=env('WX_URL').'/wx/sugg/login';
        $user=$auth->authorize($to, $scope = 'snsapi_userinfo', $state = 'STATE');
        $req->session()->put('user',$user->all());
        if($req->session()->get('user')){
            return redirect('/wx/suggesstion');
        }

    }
    public function bangding_login(Request $req){
        if($req->session()->get('user') ) {
            return redirect('/wx/card/get/p');
        }
        $auth=new Auth(env('WX_APPID'),env('WX_APPSECRET'));
        $to=env('WX_URL').'/wx/bangding/login';
        $user=$auth->authorize($to, $scope = 'snsapi_userinfo', $state = 'STATE');
        $req->session()->put('user',$user->all());
        if($req->session()->get('user')){
            return redirect('/wx/card/get/p');
        }

    }
    public function reserve_login(Request $req){
        if($req->session()->get('user') ) {
            return redirect('/wx/get/my/reserve');
        }
        $auth=new Auth(env('WX_APPID'),env('WX_APPSECRET'));
        $to=env('WX_URL').'/wx/reserve/login';
        $user=$auth->authorize($to, $scope = 'snsapi_userinfo', $state = 'STATE');
        $req->session()->put('user',$user->all());
        if($req->session()->get('user')){
            return redirect('/wx/get/my/reserve');
        }

    }
    public function logout(Request $req){
        //$req->session()->forget('user');
        echo '小伙子别跑 ~';
    }



}
