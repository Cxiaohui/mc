<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 10:40
 */
namespace app\gerent\model;
use app\common\model\General;
//use think\Db;
class Appnode extends General{
    
    public $t = 'app_power_nodes';

    public function save_data($data){
        $data['uptime'] = $this->now_datetime;

        if(isset($data['id']) && $data['id']>0){
            return $this->update_data(['id'=>$data['id']],$data);
        }else{

            return $this->add_data($data);
        }
    }


}