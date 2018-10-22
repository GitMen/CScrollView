<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use response;

class TestController extends Controller
{

    public function index(Request $req)
    {
        dd('hello,sc');
    }
    public function test(Request $req)
    {
        dd('hello,test');
    }
    public function get(Request $req,$id=1)
    {
        echo $id;
        echo $req->getPathInfo();
        echo $req->getRequestFormat();
        dd($req);
    }
    public function res(Request $req){
        $type=$req->format;
        //$type=$format;
        $code='200';
        $message='success';
        $data=[
            'title'=>'测试api返回基类标题',
            'auth'=>'dang',
            'content'=>[
                '0'=>'内容头文件',
                '1'=>'内容body',
            ],
            'time'=>time(),
            'status'=>'1',
            'power'=>'3',
        ];
        //self::jsonEncode($code,$message,$data);
        //self::xmlEncode($code,$message,$data);
        //dd($type);
        response::show($code,$message,$data,$type);
    }
    public function testPost(Request $req) {
        return $this->view('');
    }

}
