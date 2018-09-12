<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 15:52
 */
namespace app\api\controller\c_v1;
use app\common\model\Buser,
    app\common\model\Team as mT;
class Team extends Common{


    public function list_get(){
        $cache_key = config('cache_key.api_team');
        $teams = cache($cache_key);

        if($teams){
            return $this->response([
                'code'=>200,
                'msg'=>'成功',
                'data'=>[
                    'list'=>$teams
                ]]);

        }

        $teams = (new mT())->get_team_list('1=1','id,name',0);
        if(empty($teams)){
            return $this->response(['code'=>201,'msg'=>'empty','data'=>['list'=>[]]]);
        }

        $fileds = 'name,head_pic,department,post,post_desc,post_duties,team_id';
        $members = (new Buser())->get_list(['status'=>1,'is_work'=>1,'is_join_team'=>1,'isdel'=>0],$fileds,0);

        if(empty($members)){
            return $this->response(['code'=>201,'msg'=>'empty','data'=>['list'=>[]]]);
        }

        foreach($teams as $k=>$tm){
            if(!isset($teams[$k]['members'])){
                $teams[$k]['members'] = [];
            }
            foreach($members as $mk=>$mb){
                if($mb['team_id']==$tm['id']){
                    $mb['plc_url'] = c_img($mb['head_pic'],3,600);
                    unset($mb['head_pic']);
                    $teams[$k]['members'][] = $mb;
                    unset($members[$mk]);
                }
            }
        }
        cache($cache_key,$teams,600);
        return $this->response([
            'code'=>200,
            'msg'=>'成功',
            'data'=>[
                'list'=>$teams
            ]]);

    }

}