<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 15:26
 */
namespace app\common\model;

class Buser extends General{
    public $t = 'admin';

    public function get_team_list(){
        $sql = 'select ad.name,ad.head_pic,ad.department,ad.post,ad.post_desc,ad.post_duties,tn.name as team_name 
from mc_admin as ad left join `mc_team_names` as tn on tn.id=ad.team_id 
where ad.is_join_team=1 and ad.status=1 and ad.is_work=1 and ad.isdel=0 order by tn.sort asc,ad.id asc';
        return $this->query_sql($sql);
    }
}