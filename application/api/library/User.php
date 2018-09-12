<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/7/4
 * Time: 17:15
 */
namespace app\api\library;
use app\common\model\Buser,
    app\common\model\Cuser;
class User{


    static public function get_b_user($user_id){
        $cache_key = config('b_user_cache').$user_id;
        $user = cache($cache_key);
        if($user){
            return $user;
        }
        $user = (new Buser())->get_info(['id'=>$user_id,'isdel'=>0],'id,cpid,name,en_name,allow_lg_b,b_power_tag');
        //print_r($user);
        cache($cache_key,$user,300);
        return $user;
    }

    static public function get_c_user($user_id){
        $cache_key = config('c_user_cache').$user_id;
        $user = cache($cache_key);
        if($user){
            return $user;
        }
        $user = (new Cuser())->get_info(['id'=>$user_id,'isdel'=>0],'id,cpid,uname,gender,mobile');
        //print_r($user);
        cache($cache_key,$user,300);
        return $user;
    }
}