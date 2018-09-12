<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/26
 * Time: 18:15
 */
namespace app\common\model;

class Projectim extends General{
    public $t = 'project_im';

    public function save_data($data){
        $w = ['p_id'=>$data['p_id']];
        if($this->get_count($w)>0){
            return $this->update_data($w,$data);
        }
        $data['addtime'] = $this->now_datetime;
        return $this->add_data($data);
    }
}