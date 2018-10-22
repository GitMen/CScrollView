<?php

/**
 * Created by PhpStorm.
 * User: dangbowen
 * Date: 16/6/27
 * Time: 17:40
 */
//api接口类
//namespace App\libs;
class response
{
    const JSON = 'json';


    public static function jsonEncode($code,$message='',$data=array()){
        if(!is_numeric($code)){
            return '';
        }
        $result=[
            'code'      =>$code,
            'message'   =>$message,
            'data'      =>$data
        ];
        echo json_encode($result);
        exit;
    }
    public static function xmlEncode($code,$message='',$data=array()){
        if(!is_numeric($code)){
            return '';
        }
        $result=[
            'code'      =>$code,
            'message'   =>$message,
            'data'      =>$data
        ];
        header("Content-Type:text/xml");
        $xml="<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml.="<root>\n";

        $xml.=self::xmlTocode($result);

        $xml.="</root>\n";
        echo $xml;
        exit;
    }
    public static function xmlTocode($data=array()){
        $xml=$attr='';
        foreach($data as $k=>$v){
            if(is_numeric($k)){
                $attr=" id='{$k}'";
                $k="item";
            }
            $xml.="<{$k}{$attr}>";
            //$xml.="\n";
            $xml.=is_array($v) ? self::xmlTocode($v) : $v;
            $xml.="</{$k}>\n";
        }

        return $xml;
    }
    public function arrEncode($code,$message='',$data=array()){
        if(!is_numeric($code)){
            return '';
        }
        $result=[
            'code'      =>$code,
            'message'   =>$message,
            'data'      =>$data
        ];
        print_r($result);
    }
    public static function show($code , $message='' , $data=array() , $type=self::JSON){
        if(!is_numeric($code)){
            return '';
        }
        $result=[
            'code'      =>$code,
            'message'   =>$message,
            'data'      =>$data,
        ];
        if($type =='json'){
            self::jsonEncode($code,$message,$data);
            exit;
        }elseif($type =='xml'){
            self::xmlEncode($code,$message,$data);
        }elseif($type =='arr'){
            var_dump($result);
        }
    }
    public static function banks($id){
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
            case "10":
                $bank_name = "渤海银行中关村支行";
                break;
            case "11":
                $bank_name = "交通银行东单支行";
                break;
            case "12":
                $bank_name = "建设银行安慧支行";
                break;
            case "13":
                $bank_name = "北京银行太平桥支行";
                break;
            case "14":
                $bank_name = "广发银行北京分行";
                break;
            case "15":
                $bank_name = "农业银行朝东支行";
                break;
            case "16":
                $bank_name = "农业银行石景山支行";
                break;
            case "17":
                $bank_name = "农业银行顺义支行";
                break;
            case "18":
                $bank_name = "建设银行长安支行";
                break;
            default:
                $bank_name = "无";
        }
        return $bank_name;
    }
    public static function jetLag($startdate,$enddate){
//        $hour=floor((strtotime($enddate)-strtotime($startdate))%86400/3600);
        $minute=floor((strtotime($enddate)-strtotime($startdate))%86400/60);
        $time=number_format($minute/60,2).'h';
        return $time;
    }
    public static function ajaxReturn(){

}
}