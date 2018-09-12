<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/15
 * Time: 09:29
 */
namespace app\common\model;

class Approlenode extends General{
    public $t = 'app_power_role_nodes';


    public function save_access_data($rid,$access){
        $this->del_row(['role_id'=>$rid]);
        if(empty($access)){
            return false;
        }
        $data = [];
        foreach($access as $acc){
            $data[] = ['role_id'=>$rid,'node_id'=>$acc,'addtime'=>$this->now_datetime];
        }
        return $this->insert_all($data);
    }
}