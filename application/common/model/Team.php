<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/20
 * Time: 16:05
 */
namespace app\common\model;

class Team extends General {
    public $t = 'team_names';

    public function get_team_list($where,$field='*',$limit=15,$kv=false){
        $list = $this->_get_list($this->t,$where,['sort'=>'asc'],$field,$limit);
        if(!$kv){
            return $list;
        }
        return create_kv($list,'id','name');

    }
}