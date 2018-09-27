<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/28
 * Time: 00:41
 */
namespace app\common\library;
use app\common\model\Buser,
    app\gerent\model\Company;

class Buserlib{

    public function get_user_info($user_id){
        $info = (new Buser())->get_info(['id'=>$user_id],'id,name,name as uname,en_name,sex as gender,mobile,head_pic,depart_id,post,remark');
        $info['head_pic'] = c_img($info['head_pic'],3,600);

        // 20180923 部门信息
        $company = [];
        if($info && $info['depart_id']>0){
            $company = (new Company())->get_company_depart('d.id='.$info['depart_id'].' limit 1');
        }

        $info['company_name'] = $company?$company[0]['company_name']:'无';
        $info['depart_name'] = $company?$company[0]['depart_name']:'无';
        return $info;
    }
}