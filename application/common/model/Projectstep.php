<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/19
 * Time: 08:57
 */
namespace app\common\model;

//use app\common\model\General;

class Projectstep extends General{
    public $t = 'p_steps';

    public function get_step_list($where,$filed='*'){
        return $this->_get_list($this->t,$where,['level'=>'asc','step_sort'=>'asc'],$filed,0);
    }

    public function is_commplete($stepid){
        $sql = 'select ((select count(1) from mc_p_steps where pid = '.$stepid.' and isdel=0 and status=4) /(select count(1) from mc_p_steps where pid = '.$stepid.' and isdel=0) ) as t';
        $res = $this->query_sql($sql);
        return $res[0]['t'];
    }

}