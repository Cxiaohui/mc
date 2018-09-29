<?php
/**
 * Created by PhpStorm.
 * User: chenxh
 * Date: 2018/9/29
 * Time: 00:55
 */
namespace app\gerent\controller;

// 日志 信息页面
class Syslog extends Common
{

    public function _initialize($check_login=true)
    {
        parent::_initialize($check_login);
    }

    public function index()
    {

        $dir = input('get.dir','','trim');
        //$dir = str_replace('-','/',$dir);
        $path = LOG_PATH.ltrim($dir,'/');
        echo "<p>{$dir}</p>";
        $list =  $this->getdir($path);
        if(empty($list)){
            
            echo "<p>empty dir</p>";
            exit;
        }
        //print_r($list);
        foreach($list as $lt){
            $uri_path = $dir.'/'.$lt['name'];
            if($lt['isdir']==1){
                echo '<p><a href="?dir='.$uri_path.'">'.$lt['name'].'</a></p>';
            }else{
                $url = url('syslog/info').'?path='.$uri_path;
                echo '<p><a href="'.$url.'">'.$lt['name'].'</a></p>';
            }
        }
    }
    
    public function info(){
        $path = input('get.path','','trim');
        $file = LOG_PATH.$path;

        if(!file_exists($file)){
            exit('file not exists');
        }
        $content = file_get_contents($file);
        return str_replace(["\r","\n"],'<br/>',$content);
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