<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/29
 * Time: 00:55
 */
namespace app\gerent\controller;

//todo 错误 信息页面
class Syslog extends Common
{


    public function index()
    {

        $dir = input('get.dir','','trim');
        $path = LOG_PATH.$dir;

        $list =  $this->getdir($path);
        print_r($list);
    }

    private function getdir($dir)
    {
        $return = [];
        $list = scandir($dir);
        //print_r($list);exit;
        foreach ($list as $k=> $file) {

            if('.' == $file || '..' == $file){
                unset($list[$k]);
                continue;
            }else{
                $tmp = ['name'=>$file,'isdir'=>0];
                if(is_dir($dir.'/'.$file)){
                    $tmp['isdir'] = 1;
                }

                $return[] = $tmp;
            }
        }
        return $return;
    }

}