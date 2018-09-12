<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/18
 * Time: 23:16
 */
namespace app\common\model;

class Project extends General{
    public $t = 'projects';

    public function is_sejishi($p_id,$user_id){
        return $this->get_count(['id'=>$p_id,'desgin_user_id'=>$user_id,'isdel'=>0])>0;
    }

    public function is_jingli($p_id,$user_id){
        return $this->get_count(['id'=>$p_id,'manager_user_id'=>$user_id,'isdel'=>0])>0;
    }

}