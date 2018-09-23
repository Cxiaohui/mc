<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 16:31
 */
namespace app\gerent\model;
use app\common\model\General;
class Company extends General{
    public $t = 'admin_company';

    public function get_list($where,$filed='*',$limit=0){
        return $this->_get_list($this->t,$where,['sort'=>'asc'],$filed,$limit);
    }
    //todo 20180923
    public function get_company_depart(){

    }
}