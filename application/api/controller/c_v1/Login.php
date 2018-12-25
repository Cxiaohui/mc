<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 14:46
 */
namespace app\api\controller\c_v1;
use app\api\library\Login as LG,
    app\common\library\YunIM,
    app\api\library\Apitoken;

class Login extends Common{


    public function index_post(){

        $Authorization = $this->req->header('Authorization');
        if(!$Authorization){
            return $this -> response(['code' => 201, 'msg' => '无法访问']);
        }
        $auth_list = explode(':',$Authorization);
        if(count($auth_list)!=2){
            return $this -> response(['code' => 201, 'msg' => '无法访问.']);
        }

        $res = LG::c_lg_check($auth_list[0],$auth_list[1]);
        if($res['err']==1){
            return $this -> response(['code' => 201, 'msg' => $res['msg']]);
        }

        $user = $this->user_data($res['user']);

        //$uptoken = \app\common\library\Qiniu::get_uptoken(config('qiniu.bucket1'));

        return $this -> response(['code' => 200, 'msg' => '登录成功','data'=>[
            'user'=>$user
        ]]);
    }

    public function sms_login_post(){
        $mobile = input("post.mobile",'','trim');
        $vcode = input("post.vcode",'','trim');
        $os = request()->header('os');
        //cache check and delete
        $cache_key = config('cache_key.mobile_verify_code').$mobile;
        if($os != 'iOS') {
            $v_code = cache($cache_key);
            /*if(!$v_code){
                return $this -> response(['code' => 201, 'msg' => '验证码已过期']);
            }*/
            if ($vcode != $v_code) {
                return $this->response(['code' => 201, 'msg' => '验证码不正确']);
            }
        }

        //检查是否是平台用户
        $check_rs = LG::c_mobile_check($mobile);
        if($check_rs['err']!=0){
            return $this -> response(['code' => 201, 'msg' => $check_rs['msg']]);
        }

        $user = $this->user_data($check_rs['user']);

        cache($cache_key,null);
        return $this -> response(['code' => 200, 'msg' => '登录成功','data'=>[
            'user'=>$user
        ]]);
    }

    public function out_post(){
        Apitoken::clean_api_token($this->user_type,$this->user_id);
        //清除jpush绑定的别名
        \app\common\library\Jpush::del_alias($this->user_id,$this->user_type);
        return $this -> response(['code' => 200, 'msg' => '退出成功']);
    }

    //=============
    protected function user_data($user){
        //im token
        $yim = new YunIM();
        $res = $yim->createCUserID($user['id']);
        if($res['err']==1){
            return $this->response(['code'=>201,'msg'=>$res['msg']]);
        }

        $user['im_token'] = $res['token'];
        $user['im_user_id'] = $yim->build_im_userid($user['id'],$this->user_type);

        //api token
        $user['head_pic'] = c_img($user['head_pic'],2,600);
        $user['api_token'] = Apitoken::create_save_api_token($this->user_type,$user['id']);

        /*$api_token = Apitoken::get_api_token($this->user_type,$user['id']);
        if($api_token['api_token']){
            $user['api_token'] = $api_token['api_token'];
        }else{
            $user['api_token'] = Apitoken::create_save_api_token($this->user_type,$user['id']);
        }*/


        return $user;
    }
}