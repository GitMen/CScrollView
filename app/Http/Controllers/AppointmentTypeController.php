<?php
/**
 * Created by PhpStorm.
 * User: zhangdinghui
 * Date: 2017/11/6
 * Time: 下午1:21
 */

namespace App\Http\Controllers;


use App\AppointmentType;
use App\RBank;
use Illuminate\Http\Request;
use response;

class AppointmentTypeController extends Controller
{

    public static function addType(Request $request){
        $name = $request->input('name');
        //查询是否存在
        $type = AppointmentType::where('type',$name)->first();
        if($type){
            response::jsonEncode(301,'该类型已存在',['status'=>'该类型已存在']);
            return;
        }
        $addType = new AppointmentType();
        $addType->type = $name;
        if($addType->save()){
            response::jsonEncode(200,'该类型已存在',['status'=>'该类型已存在']);
        }else{
            response::jsonEncode(201,'预约类型添加失败',['status'=>'预约类型添加失败']);
        }
    }

    public static function getTypeList(){
        $list = AppointmentType::get();
        response::jsonEncode(200,'error',$list);
    }

    public static function deleteType(Request $request){
        $typeid = $request->input('type_id');
        $status = AppointmentType::where('type_id',$typeid)->delete();
        if($status){
            response::jsonEncode(200,'error','');
        }else{
            response::jsonEncode(201,'预约类型删除失败','');
        }
    }


    public static function addBank(Request $request){
        $name = $request->input('bank_name');
        //查询是否存在
        $type = RBank::where('bank_name',$name)->first();
        if($type){
            response::jsonEncode(301,'该类型已存在',['status'=>'该银行已存在']);
            return;
        }
        $addType = new RBank();
        $addType->bank_name = $name;
        if($addType->save()){
            response::jsonEncode(200,'该类型已存在',['status'=>'该类型已存在']);
        }else{
            response::jsonEncode(201,'预约类型添加失败',['status'=>'签约银行添加失败']);
        }
    }

    public static function getBankList(){
        $list = RBank::get();
        response::jsonEncode(200,'error',$list);
    }


    public static function deleteBank(Request $request){
        $typeid = $request->input('bank_id');
        $status = RBank::where('bank_id',$typeid)->delete();
        if($status){
            response::jsonEncode(200,'error','');
        }else{
            response::jsonEncode(201,'签约银行删除失败','');
        }
    }
}