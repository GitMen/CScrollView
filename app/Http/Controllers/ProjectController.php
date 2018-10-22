<?php

namespace App\Http\Controllers;

use App\RBank;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use response;
use App\Project;
use App\Bank;
use DB;
class ProjectController extends Controller
{
    public function projectAdd(Request $req){
        //var_dump($_POST);die;
        $name=$req->name;
        $banks=$req->banks;
        $project=new Project();
        $project->name=$name;
        if($project->save()){
            foreach($banks as $v){
                $bank=new Bank();
                $bank->project_id=$project->id;
                $bank->bank=$v;
                $bank->save();
            }
            response::jsonEncode(200,'success',['status'=>'项目添加成功']);
        }else{
            response::jsonEncode(400,'error',['status'=>'项目添加失败']);
        }
    }
    public function projectSelect(Request $req){
        $id=$req->id;
        $project=Project::find($id);

        if($project){
            $bank=array();
            $banks=Bank::where('project_id',$project->id)->get();
            foreach($banks as $value){
                $bank[]=[$value->bank];
            }

            $data=[
                'id' => $project->id,
                'name' => $project->name,
                'bank' => $bank,
            ];
            response::jsonEncode(200,'success',$data);

        }else{
            response::jsonEncode(200,'error',['status'=>'项目添加失败']);
        }
    }

    public function projectEdit(Request $req){
        $project=Project::find($req->id);
        $name=$req->name;
        $banks=$req->banks;

        $project->name=$name;
        if($project->save()){
            Bank::where('project_id',$project->id)->delete();
            foreach($banks as $v){
                $bank=new Bank();
                $bank->project_id=$project->id;
                $bank->bank=$v;
                $bank->save();
            }
            response::jsonEncode(200,'success',['status'=>'项目添加成功']);
        }else{
            response::jsonEncode(400,'error',['status'=>'项目添加失败']);
        }
    }
    
    
    
    
    
    
    
    
    
    
    public function projectList($page){
        $limit=1000;
        $adminall=Project::all();
        $num=$adminall->count();
        $pageall = ceil($num/$limit);
        $projects = Project::forPage($page,$limit)->orderBy('id','asc')->get();
        $data=array();
            foreach($projects as $k=>$v){
                $bank=array();
                $banks=Bank::where('project_id',$v->id)->get();
                foreach($banks as $value){
                    $bank[]=$value->bank;
                }
                $bankList = RBank::whereIn('bank_id',$bank)->get();
//                $dbdata=DB::table('projects')->leftJoin('banks','projects.id','=','banks.project_id')->get();
                $data[$k]=[
                    'id' => $v->id,
                    'name' => $v->name,
                    'bank' => $bankList,
                ];

            }
            if(empty($data)){
                response::jsonEncode(400,'error',['status'=>'当前没有项目信息']);
            }else{
                $date=[$num,$pageall,$data];
                $message='success';
                $code=200;
                response::jsonEncode($code,$message,$date);
            }
    }

}
