<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/1
 * Time: 16:09
 */
namespace app\api\controller\c_v1;
use app\common\model\Pcase as mPcase,
    app\common\model\Pcasestep,
    app\common\model\Pcasestepimg;
class Pcase extends Common{


    public function list_get(){


    }

    public function info_get(){
        $id = input('get.id',0,'int');

        if(!$id || $id<=0){
            return $this->response(['code'=>201,'msg'=>'访问错误']);
        }
        $pcase = new mPcase();
        $p_info = $pcase->get_info(['id'=>$id,'isdel'=>0]);
        if(!$p_info){
            return $this->response(['code'=>201,'msg'=>'访问错误']);
        }
        $p_cont = (new Pcasestep())->get_list(['case_id'=>$id,'isdel'=>0],'id,title,summary');
        if(!empty($p_cont)){
            $h5_base_url = $this->h5_base_url();
            $p_img = new Pcasestepimg();
            foreach($p_cont as $k=>$cont){
                $p_cont[$k]['info_url'] = $h5_base_url .'DetailsPage2.html?id='.$cont['id'];
                $p_cont[$k]['imgs'] = $p_img->get_list(['case_id'=>$id,'case_step_id'=>$cont['id']],'img_url');
            }
        }
        // 浏览数加 1
        $pcase->set_inc(['id'=>$id,'isdel'=>0],'view_num');
        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'case_info'=>$p_info,
            'case_list'=>$p_cont
        ]]);
    }


    public function step_info_get(){

        $id = input('get.id',0,'int');

        if(!$id || $id<=0){
            return $this->response(['code'=>201,'msg'=>'访问错误']);
        }
        $Pcasestep = new Pcasestep();
        $p_cont = $Pcasestep->get_info(['id'=>$id,'isdel'=>0],'id,title,content,addtime');
        if(!$p_cont){
            return $this->response(['code'=>201,'msg'=>'访问错误']);
        }
        $p_cont['addtime'] = date('m月d日',strtotime($p_cont['addtime']));

        //浏览数加 1
        $Pcasestep->set_inc(['id'=>$id,'isdel'=>0],'view_num');

        return $this->response(['code'=>200,'msg'=>'成功','data'=>[
            'info'=>$p_cont
        ]]);
    }
}