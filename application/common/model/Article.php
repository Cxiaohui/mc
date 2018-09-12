<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/15
 * Time: 15:33
 */
namespace app\common\model;

class Article extends General{
    public $t = 'articles';


    public function get_art_list($where,$field='*',$order=['id'=>'desc'],$limit=10){
        return $this->_get_list($this->t,$where,$order,$field,$limit);
    }

    public function get_full_list(){
        $sql = '';
    }
}