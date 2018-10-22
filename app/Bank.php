<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';
    protected $primaryKey='id';
    public $timestamps=false;



    public static function returnBank($pay_bank_id){
        $pay_bank = '';
        switch ($pay_bank_id) {
            case 0:
                $pay_bank = "请选择贷款银行";
                break;
            case 1:
                $pay_bank = "北京银行复兴支行";
                break;
            case 2:
                $pay_bank = "兴业银行望京支行";
                break;
            case 3:
                $pay_bank = "中信银行观湖国际支行";
                break;
            case 4:
                $pay_bank = "杭州银行东城支行";
                break;
            case 5:
                $pay_bank = "中国农业银行";
                break;
            case 6:
                $pay_bank = "公积金兴丰苑";
                break;
            case 7:
                $pay_bank = "公积金欣思达";
                break;
            case 8:
                $pay_bank = "公积金阳光新桥";
                break;
            case 9:
                $pay_bank = "其他银行";
                break;
            case 10:
                $pay_bank = "渤海银行中关村支行";
                break;
            case 11:
                $pay_bank = "交通银行东单支行";
                break;
            case 12:
                $pay_bank = "建设银行安慧支行";
                break;
            case 13:
                $pay_bank = "北京银行太平桥支行";
                break;
            case 14:
                $pay_bank = "广发银行北京分行";
                break;
            //2017-05-12 新增银行农业银行朝东支行；农业银行石景山支行；农业银行顺义支行；建设银行长安支行；
            case 15:
                $pay_bank = "农业银行朝东支行";
                break;
            case 16:
                $pay_bank = "农业银行石景山支行";
                break;
            case 17:
                $pay_bank = "农业银行顺义支行";
                break;
            case 18:
                $pay_bank = "建设银行长安支行";
                break;
        }
        return $pay_bank;
    }

}
