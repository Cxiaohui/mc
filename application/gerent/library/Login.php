<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/22
 * Time: 18:22
 */
namespace app\gerent\library;
use app\gerent\model\Syspower as mSyspower,
    app\gerent\model\Company,
    app\common\model\Buser;
class Login{

    static public function login_by_userid($user_id){
        $user = (new Buser())->get_info(['id'=>$user_id,'is_work'=>1,'isdel'=>0],
            'id as user_id,cpid,log,role_id,name,sex,status,depart_id');
        if(!$user){
            return false;
        }
        $p = (new Company())->get_info(['id'=>$user['cpid']],'id,power_tag');
        $user['cp_power_tag'] = $p['power_tag'];
        self::login_session($user);
        return true;
    }

    static public function login_session($data){

        cookie('mcuser',$data['user_id'],config('admin_lgn_status_expire'));

        session('username', $data['log']);
        session('name', $data['name']);
        session('user_id', $data['user_id']);
        session('superadmin', false);
        session('cpid', $data['cpid']);
        session('cp_power_tag',$data['cp_power_tag']);
        //是否为超级管理员
        if ($data['role_id'] == config('rbac.rbac_superman_id')) {
            session('superadmin', true);
        }
        unset($data['user_id'],$data['log'],$data['name']);
        session('info',$data);
        //引入RBAC,保存登录者的权限列表
        $acc_info = (new mSyspower())->get_my_node_info($data['role_id']);
        session('_ACCESS_LIST', $acc_info['acc_list']);
        session('acc_ids', $acc_info['acc_ids']);
        session('gids', $acc_info['gids']);
        session('gid_node_ids', $acc_info['gid_node_ids']);
        //return true;
    }

    static public  function clear_session(){
        cookie('mcuser',null);
        session(null);
        session_destroy();
    }
}