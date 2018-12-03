<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/12/3
 * Time: 10:01
 */
namespace app\common\exception;
use app\common\library\Mylog;
use Exception;
use think\Config;
use think\exception\Handle as thk_handle;

class Handle extends thk_handle{

    public function render(Exception $e)
    {
        $logdata = [
            'message'=>$e->getMessage(),
            'file'=>$e->getFile(),
            'line'=>$e->getLine(),
            'post'=>$_POST,
            'get'=>$_GET,
            'servers'=>$_SERVER
        ];

        if (Config::get('record_trace')) {
            $logdata['trace'] = $e->getTraceAsString();
        }

        Mylog::write($logdata,'exception');


        $error_meesage = Config::get('error_message')? : '有错误，请稍后再试';
        $HTTP_ACCEPT = trim(request()->server('HTTP_ACCEPT'));
        //echo $HTTP_ACCEPT;
        // 请求异常
        if($HTTP_ACCEPT == 'application/json'){
        //if ($e instanceof Exception && request()->isAjax()) {
            //return response($e->getMessage(), $e->getStatusCode())
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode([
                'code'=>500,
                'msg'=>$error_meesage,
                'err'=>1
            ],JSON_UNESCAPED_UNICODE);
            exit;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo '<h5 style="color:#d9534f;text-align: center;padding-top: 30px;">'.$error_meesage.'</h5>'
        .'<p style="text-align: center;"><a href="javascript:history.back();">返回</a></p>';
        exit;
    }
}
