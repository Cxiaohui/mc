<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 14:06
 */
namespace app\api\library;
use app\common\model\Apptokens;
class Apitoken{

    static public function check_api($user_type,$user_id,$api_token){
        $user_type = strtolower($user_type);
        $cache_key = self::get_cache_key($user_type,$user_id);
        $token = cache($cache_key);
        //echo $token;exit;
        if($token){
            //在其他设备登录
            return $token == $api_token?$user_id:-1;
        }
        $w = ['user_id'=>$user_id];
        if($user_type=='c'){
            $w['user_type'] = 2;
        }elseif($user_type=='b'){
            $w['user_type'] = 1;
        }
        $u_info = (new Apptokens())->get_info($w,'api_token,token_expiry');
        if(!$u_info){
            return -2;//
        }
        cache($cache_key,$u_info['api_token']);
        return $u_info['api_token'] == $api_token?$user_id:-3;//token过期
    }

    static public function clean_api_token($user_type,$user_id){
        $cache_key = self::get_cache_key($user_type,$user_id);
        cache($cache_key,null);
        $w = ['user_id'=>$user_id];
        if($user_type=='c'){
            $w['user_type'] = 2;
        }elseif($user_type=='b'){
            $w['user_type'] = 1;
        }
        (new Apptokens())->update_data($w,['api_token'=>'','token_expiry'=>0,'uptime'=>date('Y-m-d H:i:s')]);
    }

    static public function get_api_token($user_type,$user_id){
        $w = ['user_id'=>$user_id];
        if($user_type=='c'){
            $w['user_type'] = 2;
        }elseif($user_type=='b'){
            $w['user_type'] = 1;
        }

        return (new Apptokens())->get_info($w,'api_token');
    }

    static public function create_save_api_token($user_type,$user_id){

        $token = self::create_api_token($user_type,$user_id);
        self::save_api_token($user_type,$user_id,$token);

        $cache_key = self::get_cache_key($user_type,$user_id);
        cache($cache_key,null);
        cache($cache_key,$token);
        return $token;
    }

    static protected function create_api_token($user_type,$user_id){
        return md5(time().'-'.strtolower($user_type).'-'.$user_id);
    }

    static protected function get_cache_key($user_type,$user_id){
        $user_type = strtolower($user_type);
        $cache_key = '';
        if($user_type=='c'){
            $cache_key = config('c_api_token_cache').$user_id;
        }elseif($user_type=='b'){
            $cache_key = config('b_api_token_cache').$user_id;
        }
        return $cache_key;
    }

    static protected function save_api_token($user_type,$user_id,$api_token='',$token_expiry=null){
        if(!$api_token){
            $api_token = self::create_api_token($user_type,$user_id);
        }
        if(!$token_expiry){
            $token_expiry = date('Y-m-d H:i:s',time()+60*24*3600);
        }
        $data = [
            'user_id'=>$user_id,
            'user_type'=>$user_type=='b'?1:2,
            'api_token'=>$api_token,
            'token_expiry'=>$token_expiry,
            'uptime'=>date('Y-m-d H:i:s')
        ];
        $appt = new Apptokens();
        $w = [
            'user_id'=>$user_id,
            'user_type'=>$user_type=='b'?1:2,
        ];
        if($appt->get_count($w)>0){
            return $appt->update_data($w,$data);
        }
        return $appt->add_data($data);
    }

}
