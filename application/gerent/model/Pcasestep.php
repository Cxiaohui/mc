<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/1
 * Time: 11:03
 */
namespace app\gerent\model;
use app\common\model\General;

class Pcasestep extends General{
    public $t = 'case_steps';

    public function save_data($data){
        if(isset($data['id']) && $data['id']>0){
            $this->update_data(['id'=>$data['id']],$data);
            return  $data['id'];
        }else{
            $data['addtime'] = $this->now_datetime;
            return $this->add_data($data,true);
        }
    }
}