<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 19:07
 */
namespace app\common\library;
use app\common\model\SMS as mSMS,
    app\common\library\Mylog;
class Sms{

    static public function verify_send($to,$v_code=null){
        if(!$v_code){
            $v_code = \extend\Str::randString(6,1);
        }

        //$content = '您的验证码为：'.$v_code.',打死都不说哦';
        $config = config('md_sms');
        $param = [
            $v_code,
            $config['verify_time']
        ];
        return self::send($to,$config['verify_tpl_id'],implode(',',$param));
    }

    static public function send($to,$templateid,$param){
        $sms = new \extend\Sms_md();
        $rs = $sms->send($to,$templateid,$param);
        //
        $rs['mobile'] = $to;
        $rs['content'] = $templateid.'|'.$param;
        $rs['submit_time'] = date('Y-m-d H:i:s');
        $rs['failList'] = json_encode($rs['failList']);
        //print_r($rs);
        (new mSMS())->add_data($rs);
        if($rs['respCode']=='00000'){
            return ['err'=>0,'msg'=>'发送成功'];
        }else{
            Mylog::write([
                'to'=>$to,
                'rs'=>$rs
            ],'sms_record');
            return ['err'=>1,'msg'=>$rs['respDesc']];
        }
    }

}