<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Reserve;
use DB;
use App\Number;
use response;
class Key extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'one key guohao';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $time=date('H');
        $date=date('Y-m-d');
        $data=[];
        if($time>=20){
            //$reserves=Reserve::where('date',$date)->whereIn('progress',[1,3])->get();
            $reserves=Reserve::where('id',354)->whereIn('progress',[1,3])->get();
            foreach($reserves as $k=>$v){
                $v->progress=2;
                $v->status=2;
                $v->save();
                $number=Number::where('reserve_id',$v->id)->where('number_class',1)->first();
                if($number){
                    $number->number_push=1;
                    $number->number_status=0;
                    $number->save();
                }
                $data[$k]=['status'=>"$v->id 过号成功"];
            }
            if($data){
                response::jsonEncode(200,'success',$data);

            }else{
                response::jsonEncode(401,'error',['status'=>'没有预约要过号']);
            }
        }else{
            response::jsonEncode(400,'error',['status'=>'没有到时间']);

        }
    }
}
