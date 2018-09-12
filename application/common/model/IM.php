<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/13
 * Time: 15:18
 */
namespace app\common\model;

class IM extends General {

    public $t = 'im_list';


    public function update_imlist_data($where,$update){
        $update['uptime'] = $this->now_datetime;
        return $this->update_data($where,$update);
    }

    public function save_data($data){
        $data['uptime'] = $this->now_datetime;
        $where = [
            'user_type'=>$data['user_type'],
            'user_id'=>$data['user_id'],
            'im_type'=>$data['im_type'],
            'target_type'=>$data['target_type'],
            'target_tag'=>$data['target_tag']
        ];
        if($this->get_count($where)>0){
            return $this->update_data($where,$data);
        }
        $data['addtime'] = $this->now_datetime;

        return $this->add_data($data);
    }

    public function del_row($where){
        return $this->update_data($where,['uptime'=>$this->now_datetime,'is_del'=>1]);
    }
}