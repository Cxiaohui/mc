<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2017/6/27
 * Time: 21:15
 */
namespace app\common\library;

class Mylog{


    public static function write($mseg,$fname='test'){

        $file_name = LOG_PATH.$fname.'-'.date('Y-m-d').'.log';
        error_log(date('Y-m-d H:i:s').PHP_EOL.$mseg.PHP_EOL,3,$file_name);
    }

}