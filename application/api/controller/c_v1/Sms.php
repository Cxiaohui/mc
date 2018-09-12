<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/24
 * Time: 14:58
 */
namespace app\api\controller\c_v1;
use app\api\library\Login,
    app\common\library\Sms as Lsms;
class Sms extends Common{


    public function send_login_vcode_post(){

        $mobile = input('post.mobile','','trim');
        if(!$mobile){
            return $this -> response(['code' => 201, 'msg' => '手机号有误']);
        }
        //检查是否是平台用户
        $check_rs = Login::c_mobile_check($mobile);
        if($check_rs['err']!=0){
            return $this -> response(['code' => 201, 'msg' => $check_rs['msg']]);
        }
        $v_code = \extend\Str::randString(6,1);
        $send_res = Lsms::verify_send($mobile,$v_code);
        if($send_res['err']!=0){
            return $this -> response(['code' => 201, 'msg' => $send_res['msg']]);
        }
        //cache create
        $cache_key = config('cache_key.mobile_verify_code').$mobile;
        cache($cache_key,$v_code,960);

        return $this -> response(['code' => 200, 'msg' => '发送成功']);
    }



}