<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/6/27
 * Time: 16:20
 */
namespace extend;

class Sms_md{
    //同一个号码一分钟2条 一小时4条 一天 10条
    protected $send_url = 'https://api.miaodiyun.com/20150822/industrySMS/sendSMS';

    public function send($to,$templateid,$param){
        if(is_string($to)){
            $to = [$to];
        }
        $config = config('md_sms');
        $timestamp = date('YmdHis');
        $params = [
            'accountSid'=>$config['account_sid'],
            'timestamp'=>$timestamp,
            'sig'=>$this->get_sign($config,$timestamp),
            'to'=>implode(',',$to),
            'templateid'=>$templateid,
            'param'=>$param
            //'smsContent'=>$config['sign'].$content
        ];
        $parsm_str = http_build_query($params);
        //{"respCode":"00000","respDesc":"请求成功。","failCount":"0","failList":[],"smsId":"0794c1814fa84b9092ad40894cbeefbb"}
        $rs = Http::curl_post($this->send_url,$parsm_str);
        //print_r($rs);
        //$json = json_encode(simplexml_load_string($rs));

        return json_decode($rs,1);
    }

    protected function get_sign($config,$timestamp){
        return strtolower(md5($config['account_sid'].$config['auth_token'].$timestamp));
    }
}