<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/25
 * Time: 11:52
 */
namespace app\gerent\library;
use app\common\model\Buser,
    app\common\model\Cuser,
    app\common\model\Project,
    app\common\model\Projectadmin;
class Imlib{

    //todo 更新用户名片 20180925
    public function update_user_info($user_type,$user_id){

    }

    public function get_member_info($p_id,$members){
        $b_user_ids= [];
        $c_user_ids= [];

        if(is_string($members)){
            $members = explode(',',$members);
        }

        foreach($members as $mber){
            list($t,$id) = explode('_',$mber);
            if($t=='c'){
                $c_user_ids[] = $id;
            }else if($t=='b'){
                $b_user_ids[] = $id;
            }
        }
        $p_info = (new Project())->get_info(['id'=>$p_id],'id,owner_user_id');
        $p_admins = (new Projectadmin())->get_list(['p_id'=>$p_id],'type,b_user_id',0);

        $c_users = (new Cuser())->get_list(['id'=>['in',$c_user_ids]],'id,uname,mobile,head_pic',0);
        $b_users = (new Buser())->get_list(['id'=>['in',$b_user_ids]],'id,name as uname,mobile,head_pic',0);
        $p_admin_type = $this->p_admin_type();
        foreach($b_users as $bk=>$buser){
            $role = [];
            foreach($p_admins as $admin){
                if($buser['id']==$admin['b_user_id']){
                    $role[] = $p_admin_type[$admin['type']];
                }
            }
            $b_users[$bk]['im_id'] = 'b_'.$buser['id'];
            $b_users[$bk]['head_pic'] = c_img($buser['head_pic'], 3, 120);
            if(empty($role)){
                $b_users[$bk]['role'] = '其他';
            }else{
                $b_users[$bk]['role'] = implode(',',$role);
            }

        }

        foreach($c_users as $ck=>$cuser){
            $c_users[$ck]['im_id'] = 'c_'.$cuser['id'];
            $c_users[$ck]['head_pic'] = c_img($cuser['head_pic'], 2, 120);;
            if($cuser['id'] == $p_info['owner_user_id']){
                $c_users[$ck]['role'] = '业主客户';
            }else{
                $c_users[$ck]['role'] = '其他客户';
            }
        }

        return [
            'c_users'=>$c_users,
            'b_users'=>$b_users
        ];
    }

    protected function p_admin_type(){
        return [
            1=>'项目经理',
            2=>'客户经理',
            3=>'设计师',
            4=>'设计师助理',
            5=>'项目监理质检',
            6=>'装修管家'
        ];
    }
}