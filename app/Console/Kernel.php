<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DB;
use App\Reserve;
use App\Number;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\Key::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('inspire')
//                 ->hourly();
//        $schedule->call(function(){
//            $time=date('H');
            $date=date('Y-m-d');
//            $data=[];
//            if($time>0){
//                $reserves=Reserve::where('date',$date)->whereIn('progress',[1,3])->get();
//                foreach($reserves as $k=>$v){
//                    $v->progress=2;
//                    $v->save();
//                    $number=Number::where('reserve_id',$v->id)->where('number_class',1)->first();
//                    if($number){
//                        $number->number_push=1;
//                        $number->number_status=0;
//                        $number->save();
//                    }
//                }
//                DB::table('reserves')->where('date',$date)->whereIn('progress',[1,3])->where('halt',0)->update('progress',2);
////                }
//        })->everyMinute();

    }
}
