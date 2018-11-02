<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2017-06-13
 * Time: 16:35
 */
namespace app\common\controller;
use think\Controller;

class Base extends Controller{

    public function _initialize(){
        parent::_initialize();
    }


    public function _empty(){
        return $this->_error404();
        //return 'action, 404 not found!';
    }

    protected function _error404(){
        return $this->fetch('./404.html');
    }

    protected function loadJsCss($data, $type = 'js', $module = 'home') {
        $newData = array();
        //print_r($data);exit;
        foreach ($data as $val) {
            $val      = trim($val);
            $fpath    = '';
            $fname    = '';
            $fnamemin = '';

            if (strpos($val, 'http://') !== false || strpos($val, 'https://') !== false) {
                $newData[] = $val;
                continue;
            }

            //
            if (strpos($val, 'p:') !== false) {
                $fpath = './static/plugin/' . str_replace('p:', '', $val);
            } else {
                $fpath = './static/' . strtolower($module . '/' . $type) . '/' . $val;
            }

            $fname = $fpath . '.' . strtolower($type);

            if (!file_exists($fname)) {
                continue;
            }

            $fnamemin = $fpath . '.min.' . strtolower($type);

            $newData[] = file_exists($fnamemin) ? ltrim($fnamemin, '.') : ltrim($fname, '.');
        }

        return $newData;
    }

    protected function jsalert($mesg, $type = 0, $url = '') {
        echo '<script type="text/javascript">';
        echo 'alert("' . $mesg . '");';

        if ($type > 0) {
            //echo 'setTimeout(function(){';
            switch ($type) {
                case 1:
                    echo 'window.location.reload();';
                    break;
                case 2:
                    echo 'window.location.href="' . $url . '";';
                    break;
                case 3:
                    echo 'window.parent.location.reload();';
                    break;
                case 4:
                    echo 'window.parent.location.href="' . $url . '";';
                    break;
                case 5:
                    echo 'window.history.back();';
                    break;
                case 6:
                    echo 'window.parent.history.back();';
                    break;
                case 7:
                    echo 'window.parent.closelayer();';
                    break;
            }
            //echo '},1800)';
        }

        echo '</script>';
        exit;
    }


    protected function project_admin($type=1){
        if($type==1){
            return [
                1=>'manager_user_id',
                2=>'customer_manager_user_id',
                3=>'desgin_user_id',
                4=>'desgin_assistant_user_id',
                5=>'supervision_user_id',
                6=>'decorate_butler_user_id'
            ];
        }
        return [
            1=>'项目经理',
            2=>'客户经理',
            3=>'设计师',
            4=>'设计师助理',
            5=>'项目监理质检',
            6=>'装修管家'
        ];
    }

    protected function pay_stat(){
        return ['SUCCESS'=>'<span class="label label-success">支付成功</span>','FAIL'=>'<span class="label label-important">支付失败</span>'];
    }

    protected function trade_type(){
        return ['JSAPI'=>'公众号支付','NATIVE'=>'原生扫码支付','APP'=>'app支付','MICROPAY'=>'刷卡支付'];
    }

    protected function refund_channel(){
        return [
            ''=>'',
            'ORIGINAL'=>'原路退款',
            'BALANCE'=>'退回到余额',
            'OTHER_BALANCE'=>'原账户异常退到其他余额账户',
            'OTHER_BANKCARD'=>'原银行卡异常退到其他银行卡'
        ];
    }

    protected function refund_status(){
        return [
            ''=>'',
            'SUCCESS'=>'退款成功',
            'REFUNDCLOSE'=>'退款关闭',
            'PROCESSING'=>'退款处理中',
            'CHANGE'=>'退款异常，退款到银行发现用户的卡作废或者冻结了，导致原路退款银行卡失败，可前往商户平台（pay.weixin.qq.com）-交易中心，手动处理此笔退款。'
        ];
    }

    protected function refund_account(){
        return [
            ''=>'',
            'REFUND_SOURCE_RECHARGE_FUNDS'=>'可用余额退款/基本账户',
            'REFUND_SOURCE_UNSETTLED_FUNDS'=>'未结算资金退款'
        ];
    }
}