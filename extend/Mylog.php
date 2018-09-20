<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2017/6/27
 * Time: 21:15
 */
namespace extend;

class Mylog{


    public static function write($mseg,$fname='test'){
        if(is_array($mseg)){
            $mseg = print_r($mseg,1);
        }
        $path = LOG_PATH.$fname;
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $file_name = $path.'/'.date('Y-m-d').'.log';

        error_log(date('Y-m-d H:i:s').PHP_EOL.$mseg.PHP_EOL,3,$file_name);
    }

}