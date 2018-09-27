<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/28
 * Time: 00:48
 */
namespace app\common\library;

use app\common\model\Cuser,
    app\common\model\Project as Pject;

class Cuserlib{


    public function get_user_info($user_id){
        $info = (new Cuser())->get_info(['id'=>$user_id],'id,uname,gender,mobile,head_pic,remark');
        $info['gender_txt'] = gender_txt($info['gender']);
        $info['head_pic'] = c_img($info['head_pic'],2,600);


        //获当前的项目
        $pinfo = (new Pject())->get_list(['owner_user_id'=>$user_id,'isdel'=>0],'id,name,address',1);
        $info['project'] = count($pinfo)>0?$pinfo[0]:new \stdClass();

        return $info;
    }


}