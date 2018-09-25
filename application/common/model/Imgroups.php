<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/25
 * Time: 10:06
 */
namespace app\common\model;

class Imgroups extends General
{

    public $t = 'im_groups';


    public function save_groups($data){

        $w = ['p_id'=>$data['p_id']];
        if(isset($data['id']) && $data['id']>0){
            $w = ['id'=>$data['id']];
        }
        if(!isset($data['updatetime'])){
            $data['updatetime'] = $this->now_datetime;
        }


        if($this->get_count($w)>0){
            return $this->update_data($w,$data);
        }else{
            if(!isset($data['createtime'])){
                $data['createtime'] = $this->now_datetime;
            }

            return $this->add_data($data);
        }

    }

}