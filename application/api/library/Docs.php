<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/21
 * Time: 21:21
 */
namespace app\api\library;
use app\common\model\General;
class Docs{


    static public function get_all_project_docs($where,$limit){

        // [阶段信息文档]mc_p_step_docs中的文档，isdel=0
        // [项目报价文档]mc_project_offer_docs中的文档，isdel=0
        // [项目验收文档]mc_project_report_docs中的文档，isdel=0
        // [方案施工图主材]mc_project_static_docs中的文档，isdel=0

        $sql = 'select * from (
select file_type,file_name,file_path,addtime from mc_p_step_docs where '.$where.' and isdel=0 
union 
select file_type,file_name,file_path,addtime from mc_project_offer_docs where '.$where.' and isdel=0 
union 
select file_type,file_name,file_path,addtime from mc_project_report_docs where '.$where.' and isdel=0
union 
select file_type,file_name,file_path,addtime from mc_project_static_docs where '.$where.' and isdel=0
) as t order by addtime desc';

        if($limit>0){
            $sql .= ' limit '.$limit;
        }

        //$g = new General();
        return (new General())->query_sql($sql);
        //return (new General())->query_sql($sql,['d_where1'=>$where,'d_where2'=>$where,'d_where3'=>$where,'limt'=>$limit]);

    }

}