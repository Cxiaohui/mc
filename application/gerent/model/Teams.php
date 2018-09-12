<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/11
 * Time: 21:19
 */
namespace app\gerent\model;
use app\common\model\General;

class Teams extends General{
    public $t = 'team_names';
    //protected $t_cate_table = 'team_names';


    public function get_list($where,$field='*',$limit=15,$kv=false){
        $list = $this->_get_list($this->t,$where,['sort'=>'asc'],$field,$limit);
        if(!$kv){
            return $list;
        }
        return create_kv($list,'id','name');

    }

    public function save_data($data){
        if(isset($data['id']) && $data['id']>0){
            return $this->update_data(['id'=>$data['id']],$data);
        }else{
            return $this->add_data($data);
        }
    }




}