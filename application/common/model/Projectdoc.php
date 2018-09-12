<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/19
 * Time: 19:38
 */
namespace app\common\model;

class Projectdoc extends General{

    public $t = 'p_step_docs';

    public function get_last_one($where,$filed='*'){
        return $this->_get_order_info($this->t,$where,['addtime'=>'desc'],$filed);
    }
}