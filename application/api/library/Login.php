<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/14
 * Time: 15:25
 */
namespace app\api\library;
use app\common\model\Buser,
    app\common\model\Cuser;
class Login{

    static public function c_mobile_check($moible){
        $cuser = new Cuser();
        $uinfo = $cuser->get_info(['mobile'=>$moible,'isdel'=>0],'id,status,uname,mobile,head_pic,jpush_tag,im_token');
        if(!$uinfo){
            return ['err'=>1,'msg'=>'该用户不存在'];
        }
        if($uinfo['status']!=1){
            return ['err'=>1,'msg'=>'账号异常，请联系管理人员'];
        }

        return ['err'=>0,'msg'=>'ok','user'=>$uinfo];
    }

    static public function b_mobile_check($moible){
        $cuser = new Buser();
        $uinfo = $cuser->get_info(['mobile'=>$moible,'isdel'=>0],'id,status,name,en_name,mobile,b_power_tag,head_pic,jpush_tag,im_token');
        if(!$uinfo){
            return ['err'=>1,'msg'=>'该用户不存在'];
        }
        if($uinfo['status']!=1){
            return ['err'=>1,'msg'=>'账号异常，请联系管理人员'];
        }
        if($uinfo['b_power_tag']==0){
            return ['err'=>1,'msg'=>'无权限，请联系管理人员'];
        }

        return ['err'=>0,'msg'=>'ok','user'=>$uinfo];
    }

    static public function c_lg_check($account,$pwd){
        $cuser = new Cuser();
        $uinfo = $cuser->get_info(['mobile'=>$account,'isdel'=>0],'id,status,uname,mobile,lgpwd,lgstat,head_pic,jpush_tag,im_token');
        if(!$uinfo){
            return ['err'=>1,'msg'=>'该用户不存在'];
        }
        if($uinfo['status']!=1){
            return ['err'=>1,'msg'=>'账号异常，请联系管理人员'];
        }

        $in_pwd = create_pwd($pwd,$uinfo['lgstat']);
        if($in_pwd !== $uinfo['lgpwd']){
            return ['err'=>1,'msg'=>'账号或密码不正确'];
        }

        unset($uinfo['lgpwd'],$uinfo['lgstat']);
        $cuser->update_data(['id'=>$uinfo['id']],['loginip'=>request()->ip(),'logintime'=>date('Y-m-d H:i:s')]);
        return ['err'=>0,'msg'=>'ok','user'=>$uinfo];
    }

    static public function b_lg_check($account,$pwd){
        $buser = new Buser();
        $uinfo = $buser->get_info(['mobile'=>$account,'allow_lg_b'=>1,'is_work'=>1,'isdel'=>0],'id,pwd,stat,name,mobile,b_power_tag,head_pic,im_token');
        if(!$uinfo){
            return ['err'=>1,'msg'=>'该用户不存在'];
        }
        if($uinfo['status']!=1){
            return ['err'=>1,'msg'=>'账号异常，请联系管理人员'];
        }

        $in_pwd = create_pwd($pwd,$uinfo['stat']);
        if($in_pwd !== $uinfo['pwd']){
            return ['err'=>1,'msg'=>'账号或密码不正确'];
        }

        unset($uinfo['pwd'],$uinfo['stat']);
        $buser->update_data(['id'=>$uinfo['id']],['loginip'=>request()->ip(),'lgtime'=>time()]);
        return ['err'=>0,'msg'=>'ok','user'=>$uinfo];
    }
}