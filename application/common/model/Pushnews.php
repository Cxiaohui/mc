<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/26
 * Time: 23:59
 */
namespace app\common\model;

class Pushnews extends General{

    public $t = 'push_news';


    public function save_push_data($data){
        $data['uptime'] = $this->now_datetime;
        if(isset($data['id']) && $data['id']>0){
            $id = $data['id'];
            unset($data['id']);
            $this->update_data(['id'=>$id],$data);
            return $id;
        }else{
            $data['addtime'] = $this->now_datetime;
            return $this->add_data($data,true);
        }
    }
}