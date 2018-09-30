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

    public function save_data($data){
        if(isset($data['id']) && $data['id']>0){
            $id = $data['id'];
            return $this->update_data(['id'=>$id],$data);
        }else{
            return $this->add_data($data);
        }
    }

    public function get_list($where,$filed='*',$limit=0){
        return $this->_get_list($this->t,$where,['sort'=>'asc'],$filed,$limit);
    }
    // 20180923
    public function get_company_depart($w=''){
        $sql = 'select c.id as cpid,c.name as company_name,d.id as dp_id,d.name as depart_name from mc_admin_department as d left join mc_admin_company as c on c.id=d.cpid';
        if($w){
            $sql .= ' where '.$w;
        }
        return $this->query_sql($sql);
    }
}