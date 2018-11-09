<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/4
 * Time: 18:02
 */
namespace app\api\library;
use app\common\model\Projectdoc;
class Project{


    /**
     * 处理项目阶段数据=>树状结构
     * @param $steps
     * @param $status
     * @return array|\type
     */
    static public function create_type_steps($steps,$status){
        if(!empty($steps)){
            $doc = new Projectdoc();
            foreach($steps as $k=>$v){
                $steps[$k]['status_name'] = $status[$v['status']];
                $steps[$k]['plan_time'] = explode('|',$v['plan_time']);
                if(!isset($steps[$k]['plan_time'][1])){
                    $steps[$k]['plan_time'][1] = '';
                }
                if($v['realtime']){
                    $steps[$k]['realtime'] = explode('|',$v['realtime']);
                    if(!isset($steps[$k]['realtime'][1])){
                        $steps[$k]['realtime'][1] = '';
                    }
                }else{
                    $steps[$k]['realtime'] = ['',''];
                }
                //
                $steps[$k]['doc_count'] = $doc->get_count(['p_id'=>$v['p_id'],'p_step_id'=>$v['id']]);
            }
            return create_level_tree($steps);
        }
        return [];
    }


    static public  function get_step_type_docs($pstep,$p_id,$type){
        $step_docs = [];
        $step_ids = $pstep->get_step_list(['p_id'=>$p_id,'type'=>$type,'isdel'=>0],'id');
        if(!empty($step_ids)){
            $pdoc = new Projectdoc();
            $imgtypes = ['jpg','jpeg','png'];
            $step_ids = array2to1($step_ids,'id');

            $step_docs = $pdoc->get_order_list(
                ['p_id'=>$p_id,'is_primary'=>1,'file_type'=>['in',$imgtypes],'p_step_id'=>['in',$step_ids],'isdel'=>0],
                'id,p_step_id,file_type,file_name,file_path',
                ['seq'=>'asc'],
                0);

            if(!empty($step_docs)){
                foreach($step_docs as $k=>$v){
                    $step_docs[$k]['url'] = c_img($v['file_path'],0);
                    $step_docs[$k]['file_url'] = $step_docs[$k]['url'];
                    unset($step_docs[$k]['file_path']);
                }
            }
        }


        return $step_docs;
    }


}
