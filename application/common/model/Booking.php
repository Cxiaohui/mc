<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/13
 * Time: 23:17
 */
namespace app\common\model;

class Booking extends General{
    public $t = 'booking';

    public function get_list($where,$field='*',$limit=15){
        return $this->_get_list($this->t,$where,['do_status'=>'asc','booking_time'=>'asc'],$field,$limit);
    }

    public function get_booking_notices($where){

        $sql = 'select nt.id,nt.user_type,nt.user_id,bk.booking_time,bk.booking_content,p.name  as p_name  
from mc_notices as nt 
left join  mc_booking as bk  on (nt.type=3 and nt.target_id=bk.id)  
left join mc_projects as p on(p.id=bk.p_id) 
where '.$where;//bk.booking_time

        return $this->query_sql($sql);
    }

}