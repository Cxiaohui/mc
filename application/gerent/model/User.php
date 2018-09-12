<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 09:18
 */
namespace app\gerent\model;
use app\common\model\General;
//use think\Db;
class User extends General{

    public $t = 'users';

    public function save_user_data($data){
        //$datetime = $this->now_datetime;
        $data['uptime'] = $this->now_datetime;
        if(isset($data['id']) && $data['id']>0){
            return $this->update_data(['id'=>$data['id']],$data);

        }else{
            $data['addtime'] = $this->now_datetime;
            $data['lgpwd'] = create_pwd($data['lgpwd'],$data['lgstat']);
            return $this->add_data($data);

        }
    }

    public function del_user($id){
        return $this->update_data(['id'=>$id],['isdel'=>1]);
    }
}