<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/1
 * Time: 11:01
 */
namespace app\gerent\model;
use app\common\model\General;

class Pcase extends General{
    public $t = 'cases';


    public function save_data($data){
        if(isset($data['id']) && $data['id']>0){
            return $this->update_data(['id'=>$data['id']],$data);
        }else{
            $data['addtime'] = $this->now_datetime;
            return $this->add_data($data);
        }
    }
}