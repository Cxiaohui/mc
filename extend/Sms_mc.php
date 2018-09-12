<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/12
 * Time: 18:25
 */
namespace extend;

class Sms_mc{

    protected $send_url = 'http://www.xiangcall.com/hjt/v2sms.aspx';

    public function send($to,$content){
        if(is_string($to)){
            $to = [$to];
        }
        $config = config('sms_mc');
        $timestamp = date('YmdHis');
        $params = [
            'userid'=>$config['userid'],
            'timestamp'=>$timestamp,
            'sign'=>$this->get_sign($config,$timestamp),
            'mobile'=>implode(',',$to),
            'content'=>$config['sign'].$content,
            'action'=>'send',
            'sendTime'=>'',
            'extno'=>''
        ];
        $parsm_str = http_build_query($params);
        $rs = Http::curl_post($this->send_url,$parsm_str);

        $json = json_encode(simplexml_load_string($rs));

        return json_decode($json,1);
    }

    protected function get_sign($config,$timestamp){
        return strtolower(md5($config['account'].$config['pwd'].$timestamp));
    }

}