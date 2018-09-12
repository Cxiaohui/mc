<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 20:29
 */
namespace app\gerent\model;
use app\common\model\General;

class Recomd extends General{
    public $t = 'recommend';



    public function get_remd_list($where,$field='*',$limit=0){
        return $this->_get_list($this->t,$where,['sort'=>'asc'],$field,$limit);
    }

    public function save_data($data){
        if(isset($data['id']) && $data['id']>0) {
            $id = $data['id'];
            $this->update_data(['id'=>$id],$data);
        }else{
            $data['addtime'] = $this->now_datetime;
            $id = $this->add_data($data,true);
        }
        if(!$id){
            return false;
        }
        if($data['stable'] && $data['stable']!='self' && $data['sid']){
            $this->set_recmd($data['stable'],$data['sid'],$id);
        }
        return true;
    }


    /**
    * 更新资源表中的轮播状态
    * @param $stable
    * @param $sid
    * @param $reid
    * @param $retype
    */
    public function set_recmd($stable,$sid,$reid){
        model($stable)->where(['id'=>$sid])->update(['isrecmd'=>1,'reid'=>$reid]);
    }
}