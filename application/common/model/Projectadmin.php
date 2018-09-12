<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 22:06
 */
namespace app\common\model;

class Projectadmin extends General{

    public $t = 'project_admin';

    public function get_my_project($where){
        return $this->_get_list($this->t,$where,['type'=>'asc'],'p_id,type',0);
    }

    public function save_data($data){
        foreach($data as $da){
            $w = [
                'type'=>$da['type'],
                'p_id'=>$da['p_id']
            ];
            if($this->get_count($w)>0){
                $this->update_data($w,['b_user_id'=>$da['b_user_id'],'uptime'=>$this->now_datetime]);
            }else{
                $da['uptime'] = $this->now_datetime;
                $this->add_data($da);
            }
        }
    }

}