<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/19
 * Time: 08:57
 */
namespace app\gerent\model;

use app\common\model\General;

class Projectstep extends General{
    public $t = 'p_steps';

    public function get_step_list($where,$filed='*'){
        return $this->_get_list($this->t,$where,['level'=>'asc','step_sort'=>'asc'],$filed,0);
    }

    public function save_data($data){

        $step_id = 0;
        if(isset($data['id']) && $data['id']>0){
            $step_id = $data['id'];
            unset($data['id']);
            $this->update_data(['id'=>$step_id],$data);
            return $step_id;
        }
        $data['addtime'] = $this->now_datetime;
        return $this->add_data($data,true);
    }

}