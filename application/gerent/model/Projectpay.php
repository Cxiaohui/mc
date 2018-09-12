<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 12:40
 */
namespace app\gerent\model;
use app\common\model\General;

class Projectpay extends General{

    public $t = 'project_pay_record';

    public function get_nopay_list(){
        $today = date("Y-m-d");
        $limit_date = date('Y-m-d',strtotime("+7 days"));
        $where = "pr.paied_time=0 and pr.isdel=0 and (pr.payable_time>=$limit_date or pr.payable_time<$today)";
        $sql = 'select pr.p_id,pr.name,pr.payable,pr.payable_time,p.name as p_name,p.owner_user_id from mc_project_pay_record as pr 
          left join mc_projects as p on p.id=pr.p_id where '.$where.' group by pr.p_id';

        return $this->query_sql($sql);
    }

    public function save_data($data){


        if(isset($data['id']) && $data['id']>0){
            $id = $data['id'];
            unset($data['id']);
            return $this->update_data(['id'=>$id],$data);

        }
        $data['addtime'] = $this->now_datetime;
        return $this->add_data($data);
    }

}