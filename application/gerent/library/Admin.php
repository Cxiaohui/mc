<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/15
 * Time: 14:30
 */
namespace app\gerent\library;
use app\common\model\General as mg;
class Admin{


    static public function admin_in_depart(){

        $cache_key = config('ad_cache_key.admin_in_depart');
        $data = cache($cache_key);
        if($data){
            return $data;
        }


        $sql = 'select ad.id,ad.name,ad.post,adp.id as part_id,adp.name as dp_name  
from `mc_admin` as ad 
left join `mc_admin_department` as adp on adp.id=ad.`depart_id` 
where ad.isdel=0 and ad.is_work=1 order by adp.`sort` asc';

        $list = (new mg())->query_sql($sql);
        if(empty($list)){
            return [];
        }

        $data = [];
        foreach($list as $da){
            $tmp = [
                'user_id'=>$da['id'],
                'name'=>$da['name'],
                'post'=>$da['post']
            ];
            if(!isset($data[$da['part_id']])){
                $data[$da['part_id']] = [
                    'id'=>$da['part_id'],
                    'name'=>$da['dp_name'],
                    'members'=>[
                        $tmp
                    ]
                ];
            }else{
                $data[$da['part_id']]['members'][] = $tmp;
            }

        }
        cache($cache_key,$data,120);
        return $data;

    }

}