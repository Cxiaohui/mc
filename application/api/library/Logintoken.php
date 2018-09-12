<?php
/**
 *
 * User: XiaoHui
 * Date: 2016/10/10 16:37
 */
namespace app\api\library;
class Logintoken{

    static protected $key = 'oOwJeECFlFd0S923m fp9ON';

    /**
     * 生成登录token
     * @param $user_id
     * @return string
     */
    static public function create($user_id){

        $str = $user_id.'|'.time();
        $token = \extend\Encrypt::encode($str,self::$key);
        //$token = self::authcode(json_encode($data),'ENCODE');
        $data['token'] = $token;
        $data['user_id'] = $user_id;
        $redis_key = config('cache_key.api_log_token').$user_id;
        cache($redis_key,$data);
        return $token;
    }

    static public function remove($user_id){
        $redis_key = config('cache_key.api_log_token').$user_id;
        cache($redis_key,null);
        return;
    }
    /**
     * 解密登录token
     * @param $str
     * @param $step
     * @return array|mixed
     */
    static public function read($str,$step=2){
        if(!$str){
            return [];
        }
        $return = \extend\Encrypt::decode($str,self::$key);
        //$return = self::authcode($str,'DECODE');
        //print_r($return);
        if(!$return){
            return [];
        }
        $return = explode('|',$return);
        if(empty($return) || $return[0]<=0){
            return [];
        }
        if($step==1){
            return ['user_id'=>$return[0],'create_time'=>$return[1]];
        }
        $redis_key = config('cache_key.api_log_token').$return[0];
        $info = cache($redis_key);
        if($info && isset($info['user_id'])){
            if($str==$info['token']){
                return ['user_id'=>$return[0],'create_time'=>$return[1]];
            }
        }
        return [];
    }

}