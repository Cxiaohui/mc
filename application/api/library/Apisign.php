<?php
/**
 *
 * User: XiaoHui
 * Date: 2016/10/10 16:05
 */
namespace app\api\library;
use think\exception\ErrorException,
    extend\Pack;
class Apisign{
    static protected $appkey = 'e89513b56de32c4eb00e2c16170cedfe';
    /**
     *  验证加密
     */
    static public function verifyKey($binary='') {
        if($binary==''){
            //\extend\Mylog::write('appkey',request()->getContent());
            $binary = base64_decode(request()->getContent());
        }

        try{
            self::decode($binary, $_POST);
        }catch (ErrorException $e){
            return false;
        }

        if(isset($_POST['param'])){
            $group[1] = array_pop($_POST);
            parse_str($group[1], $param);
            $_POST = array_merge($_POST, $param);
            $group[0] = isset($_POST['time'])?$_POST['time']:0;
        }

        $group[2] = substr($binary, 32,4);
        ksort($group);

        if (strcasecmp(array_shift($_POST),self::createSign($group)) != 0) {
            return false;
        }
        //$this->lang_id = $_POST['lang'];
        //$this->app_model = $_POST['model'];
        return true;
    }

    /**
     * 加密
     * @param $param
     * @return string
     */
    static public function encrypt($param,$lang_id=1,$app_model=1) {
        $group[0] = time();

        //公共参数
        $binData = [
            //'time' => $group[0],
            //'lang' => $lang_id,
            'model'=> $app_model
        ];

        $paramBin='';
        $param['time'] = $group[0];
        if(count($param)>0){
            $group[1] = http_build_query($param);
            $paramBin = base64_encode(pack('a*',$group[1]));
        }
        $formatArray = ['n', 'n'];//'N',
        Pack::arr_pack2bin(array_values($binData), $formatArray, $group[2]);
        return base64_encode(pack('a32', self::createSign($group)).$group[2].$paramBin);
    }


    /**
     * 解密
     * @param $binary ，二进制字符串
     * @param $data
     */
    static public function decode($binary, &$data) {
        $dataFormat = '/a32sign/nlang/nmodel';
        Pack::bin_pack2arr(substr($binary,0,36), $dataFormat, $data);
        $paramBin = base64_decode(substr($binary,36));
        if(!empty($paramBin)){
            Pack::bin_pack2arr($paramBin,'/a*param',$arr);
            $data = array_merge($data,$arr);
        }
    }

    /**
     * @param $data
     * @return string
     */
    static protected function createSign($data) {
        return md5(md5(implode('', $data)).self::$appkey);
    }
}