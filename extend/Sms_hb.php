<?php
/**
 *
 * User: XiaoHui
 * Date:
 */
namespace extend;

class Sms_hb{
    private $user = '';//企业id
    private $paswd = '';//发送帐号密码
    private $dase_url = 'http://101.227.68.49:7891';
    //private $actions = ['send','overage','checkkeyword'];
    private $sign = '【Cool医人】 ';
    public function __construct(){
        $confg = config('sms.hb');
        $this->user = $confg['user'];
        $this->paswd = $confg['paswd'];
    }
    /**
     * 发送短信
     * @param $mobile
     * @param $content
     * @return array
     */
    public function send($mobile,$content){
        $url = $this->dase_url.'/mt?';
        $data = [
            'un' => $this->user,
            'pw' => $this->paswd,
            'da' => $mobile,
            'sm' => bin2hex(iconv("UTf-8", "GB2312", $this->sign.$content)),
            'dc' => 15,
            'rd' => 0
        ];
        $resp = Http::curl_get($url.http_build_query($data));
        parse_str($resp,$resp);
        if(!isset($resp['r']) || $resp['r'] == 0){
            return ['err'=>0,'mesg'=>'发送成功'];
        }else{
            return ['err'=>1,'mesg'=>'发送失败'];
        }
    }
    /**
     * 查询余额
     * @return array
     */
    public function balance(){
        $url = $this->dase_url.'/bi?';
        $data = [
            'un' => $this->user,
            'pw' => $this->paswd,
        ];
        $resp = Http::curl_get($url.http_build_query($data));
        parse_str($resp,$resp);
        return $resp;
    }
}